
// --- Post Item Component ---
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
                            {IconRetweet && <IconRetweet />}
                            <span>{post.stats.reposts}</span>
                        </button>
                        <button className="flex items-center gap-1.5 group hover:text-yellow-600 transition-colors cursor-pointer text-xs font-medium ml-auto">
                            {IconBookmark && <IconBookmark />}
                            <span>Salvar</span>
                        </button>
                    </div>
                </div>
            </div>
        </article>
    );
};
window.PostItem = PostItem;
