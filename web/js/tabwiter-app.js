const { useState, useEffect } = React;

// Icons
const Icons = window.lucideReact || window.lucide || {};
const {
    Home, Hash, User, Bell, Mail, Bookmark, MoreHorizontal, Code2,
    MessageSquare, Repeat, Share, ShieldCheck, Terminal, FileText,
    PenTool, X, Search, ExternalLink, MapPin, Calendar, ArrowLeft
} = Icons;

// --- SVGs Inline (Garantia de Interface) ---
const IconLogIn = () => <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" /><polyline points="10 17 15 12 10 7" /><line x1="15" y1="12" x2="3" y2="12" /></svg>;
const IconUserPlus = () => <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="8.5" cy="7" r="4" /><line x1="20" y1="8" x2="20" y2="14" /><line x1="23" y1="11" x2="17" y2="11" /></svg>;
const IconMenuLeft = () => <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>;
const IconMenuRight = () => <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="15" y1="3" x2="15" y2="21"></line></svg>;
const IconChevronUp = () => <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>;
const IconChevronDown = () => <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>;
const IconPlus = () => <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>;
const IconRetweet = () => <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m17 2 4 4-4 4" /><path d="M3 11v-1a4 4 0 0 1 4-4h14" /><path d="m7 22-4-4 4-4" /><path d="M21 13v1a4 4 0 0 1-4 4H3" /></svg>;
const IconBookmark = () => <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z" /></svg>;

// --- Data ---
// INITIAL_DATA will be injected by Yii2
const MOCK_USER = window.INITIAL_DATA.user || {
    name: "Visitante",
    handle: "guest",
    bio: "Faça login para interagir.",
    location: "Brasil",
    website: "tabnews.com.br",
    joined: "Hoje",
    tabcoins: 0,
    verified: false,
    followers: "0",
    following: "0"
};

const TRENDING_TABS = [
    { tag: "javascript", count: "12.5k" },
    { tag: "rust", count: "8.2k" },
    { tag: "IA", count: "5.1k" },
    { tag: "carreira", count: "3.4k" },
];

const INITIAL_POSTS = window.INITIAL_DATA.posts || [];

// --- Common Components ---

const Button = ({ children, primary, className, onClick, disabled }) => (
    <button
        onClick={onClick}
        disabled={disabled}
        className={`
            px-4 py-2 rounded-md font-medium transition-all duration-200 text-sm flex items-center justify-center gap-2
            ${primary
                ? 'bg-stone-900 text-white hover:bg-stone-800 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed'
                : 'bg-transparent text-stone-600 hover:bg-stone-100'}
            ${className}
        `}
    >
        {children}
    </button>
);

const Modal = ({ title, message, actionLabel, onConfirm, onClose, singleButton }) => (
    <div className="fixed inset-0 z-[70] flex items-center justify-center bg-black/40 backdrop-blur-sm animate-enter p-4">
        <div className="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 border border-stone-200">
            <h3 className="text-lg font-bold text-stone-900 mb-2">{title}</h3>
            <p className="text-sm text-stone-600 mb-6 leading-relaxed">{message}</p>
            <div className="flex justify-end gap-3">
                {!singleButton && <Button onClick={onClose}>Cancelar</Button>}
                <Button primary onClick={onConfirm}>{actionLabel || "Confirmar"}</Button>
            </div>
        </div>
    </div>
);

// --- Content Parser (Highlight Hashtags) ---
const ContentParser = ({ text, onHashClick }) => {
    if (!text) return null;

    // Simple parser for hashtags
    const parts = text.split(/(\s+)/);

    return (
        <span>
            {parts.map((part, index) => {
                if (part.startsWith('#') && part.length > 1) {
                    return (
                        <span
                            key={index}
                            className="text-tab-link hover:underline cursor-pointer font-medium"
                            onClick={(e) => { e.stopPropagation(); onHashClick(part); }}
                        >
                            {part}
                        </span>
                    );
                }
                return <span key={index}>{part}</span>;
            })}
        </span>
    );
};

// --- Post Sub-components ---

const VoteWidget = ({ post, isArticle, onVoteAttempt }) => {
    const [score, setScore] = useState(post.stats.score);
    const [voteState, setVoteState] = useState(0);
    const [loading, setLoading] = useState(false);

    const handleVote = (direction, e) => {
        e.stopPropagation();

        // Block interaction for guests (redirect to login handled outside or simple alert)
        if (window.INITIAL_DATA.isGuest) {
            window.location.href = window.INITIAL_DATA.urls.login;
            return;
        }

        if (isArticle) {
            onVoteAttempt();
            return;
        }

        if (loading) return;
        setLoading(true);

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(window.INITIAL_DATA.urls.upvote + '?id=' + post.id, {
            method: 'POST',
            headers: {
                'X-CSRF-Token': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(res => res.json())
            .then(data => {
                setLoading(false);
                if (data.success) {
                    setScore(data.points);
                    setVoteState(1); // Assuming upvote only for now per prompt logic
                    // Update global mana if needed?
                    const manaEl = document.getElementById('user-mana-display'); // If it exists outside React
                } else {
                    alert(data.message || 'Erro ao votar');
                }
            })
            .catch(err => {
                setLoading(false);
                alert('Erro de conexão');
            });
    };

    return (
        <div className="flex flex-col items-center gap-1 bg-stone-50 rounded-lg p-1 mr-4 min-w-[36px] border border-stone-100 h-fit">
            <button onClick={(e) => handleVote(1, e)} className={`p-1 rounded hover:bg-stone-200 transition-colors ${voteState === 1 ? 'text-tab-accent bg-green-50' : 'text-stone-400'}`}>
                <IconChevronUp />
            </button>
            <span className={`text-xs font-bold font-mono ${voteState === 1 ? 'text-tab-accent' : voteState === -1 ? 'text-tab-danger' : 'text-stone-600'}`}>
                {score}
            </span>
            <button className={`p-1 rounded hover:bg-stone-200 transition-colors text-stone-400 cursor-not-allowed opacity-50`}>
                <IconChevronDown />
            </button>
        </div>
    );
};

const PostItem = ({ post, onCommentClick, onArticleClick, onArticleVoteAttempt, onPostClick, onHashClick }) => {
    return (
        <article
            onClick={() => onPostClick(post)}
            className="border border-tab-border bg-white rounded-lg hover:border-stone-300 transition-all shadow-sm mb-4 animate-enter overflow-hidden cursor-pointer"
        >
            <div className="flex p-4">
                <div className="flex-shrink-0">
                    <VoteWidget
                        post={post}
                        isArticle={post.type === 'article'}
                        onVoteAttempt={onArticleVoteAttempt}
                    />
                </div>
                <div className="flex-1 min-w-0 pt-0.5">
                    {/* Header */}
                    <div className="flex items-center justify-between mb-1">
                        <div className="flex items-center gap-2 overflow-hidden">
                            <span className="text-sm font-bold text-tab-text hover:underline truncate">@{post.handle}</span>
                            {post.verified && ShieldCheck && <ShieldCheck size={12} className="text-blue-600 fill-blue-50 flex-shrink-0" />}
                            <span className="text-tab-muted text-xs mx-1">·</span>
                            <span className="text-tab-muted text-xs hover:underline whitespace-nowrap">{post.time}</span>
                        </div>
                        {MoreHorizontal && <div onClick={(e) => e.stopPropagation()} className="p-1 hover:bg-stone-100 rounded-full cursor-pointer"><MoreHorizontal size={14} className="text-tab-muted" /></div>}
                    </div>

                    {/* Content */}
                    {post.type === 'article' ? (
                        <div className="mt-2 group/article" onClick={(e) => { e.stopPropagation(); onArticleClick(post); }}>
                            <h2 className="text-lg font-bold text-tab-text leading-tight mb-2 group-hover/article:text-tab-link transition-colors flex items-start gap-2">
                                {post.title}
                                {ExternalLink && <ExternalLink size={14} className="text-stone-400 mt-1" />}
                            </h2>
                            <div className="text-[15px] leading-relaxed text-stone-600 font-sans line-clamp-3">
                                <ContentParser text={post.content} onHashClick={onHashClick} />
                            </div>
                            <div className="mt-2 text-xs font-bold text-tab-muted uppercase tracking-wider hover:text-tab-text transition-colors flex items-center gap-1">
                                Ler Artigo Original
                            </div>
                        </div>
                    ) : (
                        <div className="mt-1 text-[15px] leading-relaxed text-tab-text whitespace-pre-wrap">
                            <ContentParser text={post.content} onHashClick={onHashClick} />
                        </div>
                    )}

                    {/* Stats & Actions */}
                    <div className="mt-3 flex gap-6 text-tab-muted" onClick={(e) => e.stopPropagation()}>
                        <button onClick={(e) => { e.stopPropagation(); onCommentClick(post); }} className="flex items-center gap-1.5 group hover:text-blue-500 transition-colors cursor-pointer text-xs font-medium">
                            {MessageSquare && <MessageSquare size={16} className="group-hover:bg-blue-50 rounded-full p-0.5" />}
                            <span>{post.stats.comments}</span>
                        </button>
                        <button className="flex items-center gap-1.5 group hover:text-green-500 transition-colors cursor-pointer text-xs font-medium">
                            <IconRetweet />
                            <span>{post.stats.reposts}</span>
                        </button>
                        <button className="flex items-center gap-1.5 group hover:text-yellow-600 transition-colors cursor-pointer text-xs font-medium ml-auto">
                            <IconBookmark />
                            <span>Salvar</span>
                        </button>
                    </div>
                </div>
            </div>
        </article>
    );
};

// --- Pages ---

const ProfileView = ({ user, onBack, onHashClick, onPostClick }) => {
    const [activeTab, setActiveTab] = useState('all'); // all, article, post, retweet

    // Using main posts for demo
    const profilePosts = INITIAL_POSTS.filter(post => {
        if (activeTab === 'all') return true;
        if (activeTab === 'article') return post.type === 'article';
        if (activeTab === 'post') return post.type === 'tabet';
        return false;
    });

    return (
        <div className="animate-enter">
            <div className="mb-4">
                <button onClick={onBack} className="flex items-center gap-2 text-sm text-tab-muted hover:text-tab-text transition-colors font-medium">
                    {ArrowLeft && <ArrowLeft size={18} />} Voltar
                </button>
            </div>

            {/* Profile Header Minimalista (TabNews style) */}
            <div className="bg-white border border-tab-border rounded-xl p-6 shadow-sm mb-6">
                <div className="flex items-start justify-between mb-4">
                    <div>
                        <h1 className="text-2xl font-bold text-tab-text flex items-center gap-2">
                            {user.name}
                            {user.verified && ShieldCheck && <ShieldCheck size={20} className="text-blue-600 fill-blue-50" />}
                        </h1>
                        <div className="text-tab-muted font-mono text-sm">@{user.handle}</div>
                    </div>
                    <div className="text-right">
                        <div className="text-xs text-tab-muted uppercase tracking-wider font-bold mb-1">Tabcoins</div>
                        <div className="text-xl font-mono text-tab-accent font-bold">{user.tabcoins}</div>
                    </div>
                </div>

                {/* Bio como Texto Principal */}
                <div className="text-base leading-relaxed text-stone-800 whitespace-pre-wrap mb-6 font-mono text-sm bg-stone-50 p-4 rounded-lg border border-stone-100">
                    {user.bio}
                </div>

                <div className="flex gap-4 text-sm text-tab-muted border-t border-stone-100 pt-4">
                    <div><span className="font-bold text-tab-text">{user.following}</span> Seguindo</div>
                    <div><span className="font-bold text-tab-text">{user.followers}</span> Seguidores</div>
                    <div>{user.location}</div>
                    {user.website && <div className="text-tab-link hover:underline">{user.website}</div>}
                </div>
            </div>

            {/* Profile Tabs */}
            <div className="flex border-b border-tab-border mb-4">
                {['all', 'article', 'post', 'retweet'].map(tab => (
                    <button
                        key={tab}
                        onClick={() => setActiveTab(tab)}
                        className={`flex-1 pb-3 text-sm font-bold capitalize transition-colors border-b-2 
                            ${activeTab === tab ? 'border-tab-text text-tab-text' : 'border-transparent text-tab-muted hover:text-tab-text'}`}
                    >
                        {tab === 'all' ? 'Timeline' : tab === 'post' ? 'Posts' : tab === 'retweet' ? 'Retweets' : 'Artigos'}
                    </button>
                ))}
            </div>

            {/* List */}
            <div className="space-y-4">
                {profilePosts.length > 0 ? profilePosts.map(post => (
                    <PostItem
                        key={post.id}
                        post={post}
                        onHashClick={onHashClick}
                        onPostClick={onPostClick}
                        onArticleClick={() => { }}
                        onCommentClick={() => { }}
                        onArticleVoteAttempt={() => { }}
                    />
                )) : (
                    <div className="text-center py-8 text-tab-muted italic">Nada para mostrar aqui.</div>
                )}
            </div>
        </div>
    );
};

const HashtagView = ({ tag, onBack, onHashClick, onPostClick }) => {
    const tagPosts = INITIAL_POSTS.filter(post =>
        (post.content && post.content.includes(tag)) ||
        (post.tag && ('#' + post.tag) === tag)
    );

    return (
        <div className="animate-enter">
            <div className="mb-4 sticky top-[60px] bg-tab-bg z-10 py-2">
                <button onClick={onBack} className="flex items-center gap-2 text-sm text-tab-muted hover:text-tab-text transition-colors font-medium mb-4">
                    {ArrowLeft && <ArrowLeft size={18} />} Voltar
                </button>
                <h1 className="text-2xl font-bold text-tab-text flex items-center gap-2">
                    {tag}
                </h1>
                <p className="text-tab-muted text-sm">{tagPosts.length} resultados encontrados</p>
            </div>

            <div className="space-y-4">
                {tagPosts.map(post => (
                    <PostItem
                        key={post.id}
                        post={post}
                        onHashClick={onHashClick}
                        onPostClick={onPostClick}
                        onArticleClick={() => { }}
                        onCommentClick={() => { }}
                        onArticleVoteAttempt={() => { }}
                    />
                ))}
            </div>
        </div>
    );
};

const PostDetailView = ({ post, onBack, onHashClick }) => (
    <div className="animate-enter">
        <div className="sticky top-0 bg-tab-bg/95 backdrop-blur-sm z-20 py-3 border-b border-tab-border mb-4">
            <button onClick={onBack} className="flex items-center gap-2 text-lg font-bold text-tab-text hover:text-stone-600 transition-colors">
                {ArrowLeft && <ArrowLeft size={22} />} Tabet
            </button>
        </div>

        <article className="bg-white border border-tab-border rounded-xl p-6 shadow-sm mb-4">
            <div className="flex items-center gap-3 mb-4">
                <div className="w-10 h-10 rounded-lg bg-stone-200 flex items-center justify-center font-bold text-stone-500">
                    {post.handle.charAt(0).toUpperCase()}
                </div>
                <div>
                    <div className="font-bold text-lg">{post.user}</div>
                    <div className="text-tab-muted">@{post.handle}</div>
                </div>
            </div>

            <div className="text-xl leading-relaxed text-stone-900 whitespace-pre-wrap mb-4">
                <ContentParser text={post.content} onHashClick={onHashClick} />
            </div>

            <div className="text-sm text-tab-muted border-b border-stone-100 pb-4 mb-4">
                {post.time} · TabWiter Web App
            </div>

            <div className="flex justify-around items-center text-tab-muted px-2 py-2">
                <div className="flex flex-col items-center">
                    <span className="font-bold text-tab-text text-sm">{post.stats.reposts}</span>
                    <span className="text-xs uppercase">Retweets</span>
                </div>
                <div className="flex flex-col items-center">
                    <span className="font-bold text-tab-text text-sm">{post.stats.score}</span>
                    <span className="text-xs uppercase">Pontos</span>
                </div>
                <div className="flex flex-col items-center">
                    <span className="font-bold text-tab-text text-sm">{post.stats.comments}</span>
                    <span className="text-xs uppercase">Comentários</span>
                </div>
            </div>
        </article>

        <div className="bg-white border border-tab-border rounded-xl p-6 shadow-sm">
            <h3 className="font-bold mb-4">Respostas</h3>
            {post.commentsData && post.commentsData.length > 0 ? (
                post.commentsData.map((c, i) => (
                    <div key={i} className="border-b border-stone-100 last:border-0 py-4">
                        <div className="flex justify-between items-start mb-1">
                            <span className="font-bold text-sm">@{c.handle}</span>
                            <span className="text-xs text-tab-muted">{c.time}</span>
                        </div>
                        <p className="text-stone-800 text-sm">{c.content}</p>
                    </div>
                ))
            ) : (
                <p className="text-stone-500 text-sm italic py-4 text-center">Ninguém respondeu ainda. Comece a conversa.</p>
            )}
        </div>
    </div>
);

const CreatePostBox = ({ onPostCreate }) => {
    const [content, setContent] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSubmit = () => {
        if (!content.trim()) return;

        if (window.INITIAL_DATA.isGuest) {
            window.location.href = window.INITIAL_DATA.urls.login;
            return;
        }

        setLoading(true);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(window.INITIAL_DATA.urls.createPost, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                'content': content
            })
        })
            .then(res => res.json())
            .then(data => {
                setLoading(false);
                if (data.success) {
                    setContent('');
                    onPostCreate(data.post);
                } else {
                    alert('Erro ao postar');
                }
            })
            .catch(err => {
                setLoading(false);
                alert('Erro de conexão');
            });
    };

    return (
        <div className="bg-white border border-tab-border rounded-lg p-4 mb-6 shadow-sm flex gap-4 items-start hover:border-stone-300 transition-colors">
            <div className="w-10 h-10 rounded-full bg-stone-200 flex-shrink-0 flex items-center justify-center font-bold text-stone-500">
                {window.INITIAL_DATA.user ? window.INITIAL_DATA.user.handle.charAt(0).toUpperCase() : 'G'}
            </div>
            <div className="flex-1">
                <textarea
                    value={content}
                    onChange={(e) => setContent(e.target.value)}
                    className="w-full resize-none border-none focus:ring-0 text-stone-700 placeholder-stone-400 bg-transparent text-lg"
                    placeholder="O que você está pensando?"
                    rows={2}
                />
                <div className="flex justify-end pt-2 border-t border-stone-100 mt-2">
                    <Button primary onClick={handleSubmit} disabled={loading || !content.trim()}>
                        {loading ? '...' : 'Tabetar'}
                    </Button>
                </div>
            </div>
        </div>
    );
};

const MainApp = () => {
    // UI State
    const [view, setView] = useState('feed'); // feed, profile, post, hashtag
    const [selectedPost, setSelectedPost] = useState(null);
    const [selectedTag, setSelectedTag] = useState(null);

    const [showLeftSidebar, setShowLeftSidebar] = useState(false);
    const [showRightSidebar, setShowRightSidebar] = useState(false);

    const [showTabModal, setShowTabModal] = useState(false);
    const [modalConfig, setModalConfig] = useState(null);

    // Data State
    const [posts, setPosts] = useState(INITIAL_POSTS);
    const [tabs, setTabs] = useState([
        { id: 'home', label: 'Home', filter: { type: 'all', tag: '' } },
        { id: 'articles', label: 'Artigos', filter: { type: 'article', tag: '' } },
        { id: 'rust', label: 'Rust', filter: { type: 'all', tag: 'rust' } }
    ]);
    const [activeTabId, setActiveTabId] = useState('home');

    const handleCreateTab = (config) => {
        // Mock implementation
        const newTab = {
            id: Date.now().toString(),
            label: config.name,
            filter: { type: config.filterType, tag: config.tag.toLowerCase().trim() }
        };
        setTabs([...tabs, newTab]);
        setActiveTabId(newTab.id);
        setShowTabModal(false);
    };

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
        // Transform API post format to React App format if needed
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

    // --- Render Content ---

    const activeTab = tabs.find(t => t.id === activeTabId);
    const filteredPosts = posts.filter(post => {
        const typeMatch = activeTab.filter.type === 'all' || post.type === activeTab.filter.type;
        const tagMatch = !activeTab.filter.tag || (post.content && post.content.toLowerCase().includes(activeTab.filter.tag));
        return typeMatch && tagMatch;
    });

    return (
        <div className="min-h-screen flex justify-center bg-tab-bg text-tab-text font-sans">

            {/* Popups */}
            {/* TabConfigModal implementation skipped for brevity, easy to add back if needed */}
            {modalConfig && <Modal {...modalConfig} />}

            {/* --- Left Sidebar --- */}
            <>
                {/* Left Sidebar Overlay */}
                {showLeftSidebar && (
                    <div
                        className="fixed inset-0 bg-black/20 z-40"
                        onClick={() => setShowLeftSidebar(false)}
                    ></div>
                )}
                <aside className={`
                fixed left-0 top-0 h-screen bg-white border-r border-tab-border z-50 transition-transform duration-300
                ${showLeftSidebar ? 'translate-x-0' : '-translate-x-full'} 
                w-64
                overflow-hidden
            `}>
                    <div className="p-6 h-full flex flex-col w-64">
                        <div className="flex items-center gap-3 mb-8">
                            {Terminal && <Terminal size={24} />}
                            <span className="font-bold text-xl">TabWiter</span>
                        </div>
                        <nav className="flex flex-col gap-2">
                            <div onClick={() => { setView('feed'); setShowLeftSidebar(false); }} className={`flex items-center gap-4 p-3 rounded-lg cursor-pointer ${view === 'feed' ? 'bg-stone-100 font-bold' : 'hover:bg-stone-50 text-tab-muted'}`}>
                                {Home && <Home size={20} />} <span className="text-base">Home</span>
                            </div>
                            <div onClick={() => { if (window.INITIAL_DATA.isGuest) { window.location.href = window.INITIAL_DATA.urls.login; } else { setView('profile'); } setShowLeftSidebar(false); }} className={`flex items-center gap-4 p-3 rounded-lg cursor-pointer ${view === 'profile' ? 'bg-stone-100 font-bold' : 'hover:bg-stone-50 text-tab-muted'}`}>
                                {User && <User size={20} />} <span className="text-base">Perfil</span>
                            </div>
                            <div className="mt-auto pt-4 border-t border-stone-100">
                                {window.INITIAL_DATA.isGuest ? (
                                    <div className="flex flex-col gap-2">
                                        <a href={window.INITIAL_DATA.urls.login} className="flex items-center gap-4 p-3 rounded-lg cursor-pointer hover:bg-stone-50 text-tab-text">
                                            <IconLogIn /> <span className="text-base font-bold">Entrar</span>
                                        </a>
                                        <a href={window.INITIAL_DATA.urls.signup} className="flex items-center gap-4 p-3 rounded-lg cursor-pointer hover:bg-stone-50 text-tab-accent">
                                            <IconUserPlus /> <span className="text-base font-bold">Criar Conta</span>
                                        </a>
                                    </div>
                                ) : (
                                    <a href={window.INITIAL_DATA.urls.logout} data-method="post" className={`flex items-center gap-4 p-3 rounded-lg cursor-pointer hover:bg-red-50 text-red-600`}>
                                        {X && <X size={20} />} <span className="text-base">Sair</span>
                                    </a>
                                )}
                            </div>
                        </nav>
                    </div>
                </aside>
            </>

            {/* --- Center Content Area --- */}
            <main className={`
                flex-1 min-h-screen transition-all duration-300
                ${!window.INITIAL_DATA.isGuest && showLeftSidebar ? 'sm:ml-64' : ''} 
                ${!window.INITIAL_DATA.isGuest && showRightSidebar ? 'lg:mr-80' : ''}
            `}>
                <div className="max-w-3xl mx-auto w-full">

                    {/* --- HEADER CONTEXTUAL --- */}
                    <div className="sticky top-0 z-30 bg-tab-bg/95 backdrop-blur-sm border-b border-tab-border px-4 py-3 flex justify-between items-center shadow-sm">
                        <div className="flex items-center gap-3">
                            {view === 'feed' ? (
                                <button onClick={() => setShowLeftSidebar(!showLeftSidebar)} className="p-2 rounded-md hover:bg-stone-200 text-tab-muted">
                                    <IconMenuLeft />
                                </button>
                            ) : (
                                <button onClick={() => setView('feed')} className="p-2 rounded-md hover:bg-stone-200 text-tab-text transition-colors" title="Voltar para Home">
                                    {ArrowLeft && <ArrowLeft size={20} />}
                                </button>
                            )}

                            <h1 className="font-bold text-lg tracking-tight">
                                {view === 'feed' ? 'Feed' : view === 'profile' ? 'Perfil' : view === 'hashtag' ? 'Tópico' : 'Detalhes'}
                            </h1>
                        </div>
                        <button onClick={() => setShowRightSidebar(!showRightSidebar)} className="p-2 rounded-md hover:bg-stone-200 text-tab-muted">
                            <IconMenuRight />
                        </button>
                    </div>

                    {/* --- VIEW ROUTER --- */}
                    <div className="p-4 sm:p-6">
                        {view === 'feed' && (
                            <>
                                {/* Tabs Bar */}
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
                                        <IconPlus />
                                    </button>
                                </div>

                                {/* Create Post Box */}
                                <CreatePostBox onPostCreate={handlePostCreate} />

                                {/* Feed List */}
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
            </main>

            {/* Right Sidebar Overlay */}
            {
                showRightSidebar && (
                    <div
                        className="fixed inset-0 bg-black/20 z-40"
                        onClick={() => setShowRightSidebar(false)}
                    ></div>
                )
            }
            <aside className={`
                fixed right-0 top-0 h-screen bg-white border-l border-tab-border z-50 transition-transform duration-300
                ${showRightSidebar ? 'translate-x-0' : 'translate-x-full'} 
                w-80
                overflow-hidden
            `}>
                <div className="p-6 h-full w-80 overflow-y-auto">
                    {/* Search in Sidebar */}
                    <div className="relative group mb-6">
                        <div className="absolute left-3 top-2.5 text-tab-muted group-focus-within:text-tab-accent">
                            {Search && <Search size={18} />}
                        </div>
                        <input type="text" placeholder="Buscar no TabWiter" className="w-full bg-stone-50 border border-stone-200 focus:bg-white focus:border-tab-accent py-2 pl-10 pr-4 rounded-lg text-sm focus:outline-none transition-all" />
                    </div>

                    {window.INITIAL_DATA.isGuest && (
                        <div className="bg-blue-50 p-4 rounded-lg mb-6 text-center">
                            <h3 className="font-bold mb-2">Junte-se ao TabWiter</h3>
                            <div className="flex flex-col gap-2">
                                <a href={window.INITIAL_DATA.urls.signup} className="bg-tab-text text-white py-2 rounded font-bold">Criar conta</a>
                                <a href={window.INITIAL_DATA.urls.login} className="text-tab-text font-bold">Entrar</a>
                            </div>
                        </div>
                    )}

                    <h2 className="font-bold text-lg mb-4">Trending</h2>
                    <div className="flex flex-col gap-2">
                        {TRENDING_TABS.map((topic, i) => (
                            <div key={i} onClick={() => handleHashClick(`#${topic.tag}`)} className="flex justify-between items-center cursor-pointer hover:bg-stone-50 p-3 rounded-lg border border-stone-100 transition-colors">
                                <div className="font-bold text-tab-text">#{topic.tag}</div>
                                <div className="text-xs text-tab-muted">{topic.count}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </aside>
        </div >
    );
};

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(<MainApp />);
