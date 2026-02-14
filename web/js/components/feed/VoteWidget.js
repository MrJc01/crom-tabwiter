
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
                {IconChevronUp && <IconChevronUp />}
            </button>
            <span className={`text-xs font-bold font-mono ${voteState === 1 ? 'text-tab-accent' : voteState === -1 ? 'text-tab-danger' : 'text-stone-600'}`}>
                {score}
            </span>
            <button className={`p-1 rounded hover:bg-stone-200 transition-colors text-stone-400 cursor-not-allowed opacity-50`}>
                {IconChevronDown && <IconChevronDown />}
            </button>
        </div>
    );
};
window.VoteWidget = VoteWidget;
