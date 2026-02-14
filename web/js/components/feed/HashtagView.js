
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
window.HashtagView = HashtagView;
