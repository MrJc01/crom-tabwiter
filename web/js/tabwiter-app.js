
const { useState, useEffect, useRef } = React;

// --- Destructure globals exposed by other scripts ---
// Icons (from Icons.js via Object.assign(window, Icons))
const {
    Home, Hash, User, Bell, Mail, Bookmark, MoreHorizontal, Code2,
    MessageSquare, Repeat, Share, ShieldCheck, Terminal, FileText,
    PenTool, X, Search, ExternalLink, MapPin, Calendar, ArrowLeft, Globe,
    Activity
} = window;

// Inline SVG Icons (from Icons.js)
const { IconLogIn, IconUserPlus, IconMenuLeft, IconMenuRight, IconChevronUp, IconChevronDown, IconPlus, IconRetweet, IconBookmark } = window;

// Data (from mockData.js)
const { MOCK_USER, TRENDING_TABS, INITIAL_POSTS, MOCK_CHATS, MOCK_COMMUNITIES } = window;

// UI Components (from Button.js, Modal.js)
const { Button, Modal } = window;

// Layout Components
const { BrazilButtons, NotificationDrawer, ChatDock, ChatClipTab, ChatPanel, SidebarLeft, SidebarRight } = window;

// Feed Components
const { VoteWidget, ContentParser, PostItem, CreatePostBox, HashtagView, PostDetailView } = window;

// Profile Components
const { ProfileView } = window;

const MainApp = () => {
    // UI State
    const [view, setView] = useState('feed'); // feed, profile, post, hashtag
    const [showUniverses, setShowUniverses] = useState(false); // Universes Slide State

    // Remove Static Loader when App Mounts
    useEffect(() => {
        if (window.removeLoader) {
            window.removeLoader();
        }
    }, []);

    const [selectedPost, setSelectedPost] = useState(null);
    const [selectedTag, setSelectedTag] = useState(null);

    const [showLeftSidebar, setShowLeftSidebar] = useState(false);
    const [showRightSidebar, setShowRightSidebar] = useState(false);

    const [modalConfig, setModalConfig] = useState(null);
    const [showNotifications, setShowNotifications] = useState(false);
    const [showTabModal, setShowTabModal] = useState(false); // Added missing state

    // Chat State
    const [isChatOpen, setIsChatOpen] = useState(false);
    const [isChatMaximized, setIsChatMaximized] = useState(false);

    // Data State
    const [posts, setPosts] = useState(INITIAL_POSTS);
    const [tabs, setTabs] = useState([
        { id: 'home', label: 'Home', filter: { type: 'all', tag: '' } },
        { id: 'articles', label: 'Artigos', filter: { type: 'article', tag: '' } },
        { id: 'rust', label: 'Rust', filter: { type: 'all', tag: 'rust' } }
    ]);
    const [activeTabId, setActiveTabId] = useState('home');

    // Message listener to close Universes from iframe
    useEffect(() => {
        const handleMessage = (event) => {
            // Check origin if necessary
            if (event.data === 'closeUniverses') {
                setShowUniverses(false);
            }
        };
        window.addEventListener('message', handleMessage);
        return () => window.removeEventListener('message', handleMessage);
    }, []);

    const handleArticleClick = (post) => {
        setModalConfig({
            title: "Conteúdo Externo",
            message: `O artigo "${post.title}" é um conteúdo extenso ou link externo. Deseja abrir a fonte original para leitura completa?`,
            actionLabel: "Abrir Artigo",
            onConfirm: () => {
                console.log("Opening article:", post.id);
                setModalConfig(null);
            },
            onClose: () => setModalConfig(null)
        });
    };

    const handleArticleVoteAttempt = () => {
        setModalConfig({
            title: "Votação na Origem",
            message: "Para garantir a qualidade do ranking, votos em artigos devem ser realizados na página original do conteúdo.",
            actionLabel: "Entendi",
            singleButton: true,
            onConfirm: () => setModalConfig(null),
            onClose: () => setModalConfig(null)
        });
    };

    const handleCommentClick = (post) => {
        if (post.type === 'article') {
            handleArticleClick(post);
        } else {
            setSelectedPost(post);
            setView('post');
        }
    };

    const handlePostClick = (post) => {
        if (post.type === 'article') return;
        setSelectedPost(post);
        setView('post');
    };

    const handleHashClick = (tag) => {
        setSelectedTag(tag);
        setView('hashtag');
    };

    const handlePostCreate = (newPost) => {
        const formattedPost = {
            id: newPost.id,
            type: 'tabet', // Default
            user: newPost.username,
            handle: newPost.username, // Handle same as user for now
            time: newPost.created_at,
            content: newPost.content,
            stats: { comments: 0, reposts: 0, score: newPost.points },
            verified: false,
            commentsData: []
        };
        setPosts([formattedPost, ...posts]);
    };

    const activeTab = tabs.find(t => t.id === activeTabId);
    const filteredPosts = posts.filter(post => {
        const typeMatch = activeTab.filter.type === 'all' || post.type === activeTab.filter.type;
        const tagMatch = !activeTab.filter.tag || (post.content && post.content.toLowerCase().includes(activeTab.filter.tag));
        return typeMatch && tagMatch;
    });

    return (
        <div className="min-h-screen flex justify-center bg-tab-bg text-tab-text font-sans overflow-x-hidden">

            {/* Popups */}
            {modalConfig && <Modal {...modalConfig} />}

            <NotificationDrawer isOpen={showNotifications} onClose={() => setShowNotifications(false)} />

            {/* Chat Dock System */}
            <ChatDock
                isOpen={isChatOpen}
                toggleOpen={() => setIsChatOpen(!isChatOpen)}
                isMaximized={isChatMaximized}
                toggleMaximize={() => setIsChatMaximized(!isChatMaximized)}
            />

            {/* --- Left Sidebar --- */}
            <SidebarLeft
                showLeftSidebar={showLeftSidebar}
                setShowLeftSidebar={setShowLeftSidebar}
                view={view}
                setView={setView}
                setShowUniverses={setShowUniverses}
            />

            {/* --- Center Content Area --- */}
            <div className={`
                flex-1 min-h-screen transition-all duration-300
                ${!window.INITIAL_DATA.isGuest && showLeftSidebar ? 'sm:ml-64' : ''} 
                ${!window.INITIAL_DATA.isGuest && showRightSidebar ? 'lg:mr-80' : ''}
                relative
                overflow-hidden
            `}>

                {/* --- HEADER CONTEXTUAL (Sticky) --- */}
                <div className="sticky top-0 z-30 bg-tab-bg/95 backdrop-blur-sm border-b border-tab-border px-4 py-3 flex justify-between items-center shadow-sm h-[60px] relative">
                    <div className="flex items-center gap-3">
                        {view === 'feed' ? (
                            <button onClick={() => setShowLeftSidebar(!showLeftSidebar)} className="p-2 rounded-md hover:bg-stone-200 text-tab-muted">
                                {IconMenuLeft && <IconMenuLeft />}
                            </button>
                        ) : (
                            <button onClick={() => setView('feed')} className="p-2 rounded-md hover:bg-stone-200 text-tab-text transition-colors" title="Voltar para Home">
                                {ArrowLeft && <ArrowLeft size={20} />}
                            </button>
                        )}

                        <h1 className="font-bold text-lg tracking-tight">
                            {showUniverses ? 'Universos' : (view === 'feed' ? 'Feed' : view === 'profile' ? 'Perfil' : view === 'hashtag' ? 'Tópico' : 'Detalhes')}
                        </h1>
                    </div>

                    {/* Brazil Buttons Positioned Absolutely */}
                    <BrazilButtons onGreenClick={() => setShowNotifications(true)} />

                    <div className="flex items-center gap-2">
                        {/* Universes Toggle Button */}
                        <button
                            onClick={() => setShowUniverses(!showUniverses)}
                            className={`p-2 rounded-md transition-colors ${showUniverses ? 'bg-tab-accent text-white' : 'text-tab-muted hover:bg-stone-200'}`}
                            title="Universos"
                        >
                            {Globe ? <Globe size={20} /> : <span className="font-bold">U</span>}
                        </button>

                        <button onClick={() => setShowRightSidebar(!showRightSidebar)} className="p-2 rounded-md hover:bg-stone-200 text-tab-muted">
                            {IconMenuRight && <IconMenuRight />}
                        </button>
                    </div>
                </div>

                {/* --- CONTENT AREA (Feed + Chat split) --- */}
                <div className="flex w-full overflow-hidden" style={{ height: 'calc(100vh - 60px)' }}>

                    {/* === Feed / Universes Side === */}
                    <div className={`
                        h-full transition-all duration-500 ease-in-out flex-shrink-0
                        ${isChatMaximized
                            ? 'w-0 overflow-hidden'
                            : isChatOpen
                                ? 'hidden lg:block lg:w-[60%]'
                                : 'w-full'
                        }
                    `}>
                        {/* Sliding container for Feed <-> Universes */}
                        <div className="relative w-full h-full overflow-hidden">
                            <div
                                className={`flex w-[200%] h-full transition-transform duration-500 ease-in-out will-change-transform ${showUniverses ? '-translate-x-1/2' : 'translate-x-0'}`}
                            >
                                {/* LEFT: FEED */}
                                <div className="w-1/2 shrink-0 h-full overflow-y-auto custom-scrollbar">
                                    <div className="max-w-3xl mx-auto w-full p-4 sm:p-6 pb-20">
                                        {view === 'feed' && (
                                            <>
                                                <div className="overflow-x-auto hide-scrollbar flex items-center gap-6 mb-6 border-b border-tab-border pb-0">
                                                    {tabs.map(tab => (
                                                        <button
                                                            key={tab.id}
                                                            onClick={() => setActiveTabId(tab.id)}
                                                            className={`pb-3 px-1 text-sm font-bold whitespace-nowrap border-b-2 transition-all duration-200 ${activeTabId === tab.id ? 'text-tab-text border-tab-text' : 'text-tab-muted border-transparent hover:text-tab-text'}`}
                                                        >
                                                            {tab.label}
                                                        </button>
                                                    ))}
                                                    <button onClick={() => setShowTabModal(true)} className="pb-3 px-1 text-tab-muted hover:text-tab-accent">
                                                        {IconPlus && <IconPlus />}
                                                    </button>
                                                </div>

                                                <CreatePostBox onPostCreate={handlePostCreate} />

                                                <div className="space-y-4">
                                                    {filteredPosts.map(post => (
                                                        <PostItem
                                                            key={post.id}
                                                            post={post}
                                                            onArticleClick={handleArticleClick}
                                                            onArticleVoteAttempt={handleArticleVoteAttempt}
                                                            onCommentClick={handleCommentClick}
                                                            onPostClick={handlePostClick}
                                                            onHashClick={handleHashClick}
                                                        />
                                                    ))}
                                                    {filteredPosts.length === 0 && <p className="text-center text-tab-muted">Nenhum post encontrado.</p>}
                                                </div>
                                            </>
                                        )}

                                        {view === 'profile' && (
                                            <ProfileView
                                                user={MOCK_USER}
                                                onBack={() => setView('feed')}
                                                onHashClick={handleHashClick}
                                                onPostClick={handlePostClick}
                                            />
                                        )}

                                        {view === 'hashtag' && selectedTag && (
                                            <HashtagView
                                                tag={selectedTag}
                                                onBack={() => setView('feed')}
                                                onHashClick={handleHashClick}
                                                onPostClick={handlePostClick}
                                            />
                                        )}

                                        {view === 'post' && selectedPost && (
                                            <PostDetailView
                                                post={selectedPost}
                                                onBack={() => setView('feed')}
                                                onHashClick={handleHashClick}
                                            />
                                        )}
                                    </div>
                                </div>

                                {/* RIGHT: UNIVERSES IFRAME */}
                                <div className="w-1/2 shrink-0 h-full bg-stone-900">
                                    <iframe
                                        src={window.INITIAL_DATA.urls.universes}
                                        className="w-full h-full border-none"
                                        title="Universos"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* === Chat Panel (inline, shares space) === */}
                    {isChatOpen && (
                        <div className={`
                            h-full border-l border-tab-border transition-all duration-500 ease-in-out flex-shrink-0
                            ${isChatMaximized
                                ? 'w-full'
                                : 'w-full lg:w-[40%]'
                            }
                        `}>
                            <ChatPanel
                                onClose={() => { setIsChatOpen(false); setIsChatMaximized(false); }}
                                isMaximized={isChatMaximized}
                                toggleMaximize={() => setIsChatMaximized(!isChatMaximized)}
                            />
                        </div>
                    )}
                </div>
            </div>

            {/* --- Right Sidebar --- */}
            <SidebarRight
                showRightSidebar={showRightSidebar}
                setShowRightSidebar={setShowRightSidebar}
                onHashClick={handleHashClick}
            />
        </div>
    );
};

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(<MainApp />);
