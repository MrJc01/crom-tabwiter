
// Dependencies from window
const { ArrowLeft: ArrowLeftPD } = window;
const { ContentParser: ContentParserPD } = window;

const PostDetailView = ({ post, onBack, onHashClick }) => (
    <div className="animate-enter">
        <div className="sticky top-0 bg-tab-bg/95 backdrop-blur-sm z-20 py-3 border-b border-tab-border mb-4">
            <button onClick={onBack} className="flex items-center gap-2 text-lg font-bold text-tab-text hover:text-stone-600 transition-colors">
                {ArrowLeftPD && <ArrowLeftPD size={22} />} Tabet
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
                <ContentParserPD text={post.content} onHashClick={onHashClick} />
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
window.PostDetailView = PostDetailView;
