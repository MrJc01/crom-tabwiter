
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
window.Button = Button;
