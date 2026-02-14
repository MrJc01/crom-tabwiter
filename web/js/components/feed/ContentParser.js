
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
                return part;
            })}
        </span>
    );
};
window.ContentParser = ContentParser;
