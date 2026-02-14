
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
window.ProfileView = ProfileView;
