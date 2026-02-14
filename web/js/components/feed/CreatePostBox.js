
const { useState: useStateCPB } = React;

// Dependencies from window
const { Button: ButtonCPB } = window;

const CreatePostBox = ({ onPostCreate }) => {
    const [content, setContent] = useStateCPB('');
    const [loading, setLoading] = useStateCPB(false);

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
                    <ButtonCPB primary onClick={handleSubmit} disabled={loading || !content.trim()}>
                        {loading ? '...' : 'Tabetar'}
                    </ButtonCPB>
                </div>
            </div>
        </div>
    );
};
window.CreatePostBox = CreatePostBox;
