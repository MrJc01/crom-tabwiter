
// --- Brazil Header Components ---
const BrazilButtons = ({ onGreenClick }) => {
    return (
        <div className="absolute top-full right-32 flex gap-3 h-auto z-40 items-start">
            {/* Green */}
            <div className="group flex flex-col items-center">
                <button
                    onClick={onGreenClick}
                    className="w-10 h-3 group-hover:h-16 bg-[#009c3b] rounded-b-lg shadow-md transition-all duration-300 ease-in-out flex items-end justify-center pb-3 overflow-hidden"
                >
                    <span className="material-symbols-rounded text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-2xl select-none">notifications</span>
                </button>
            </div>
            {/* Yellow */}
            <div className="group flex flex-col items-center">
                <button
                    className="w-10 h-3 group-hover:h-14 bg-[#ffdf00] rounded-b-lg shadow-md transition-all duration-300 ease-in-out flex items-end justify-center pb-3 overflow-hidden"
                >
                    <span className="material-symbols-rounded text-[#002776] opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-2xl select-none">trending_up</span>
                </button>
            </div>
            {/* Blue */}
            <div className="group flex flex-col items-center">
                <button
                    className="w-10 h-3 group-hover:h-12 bg-[#002776] rounded-b-lg shadow-md transition-all duration-300 ease-in-out flex items-end justify-center pb-3 overflow-hidden"
                >
                    <span className="material-symbols-rounded text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-2xl select-none">chat</span>
                </button>
            </div>
        </div>
    );
};
window.BrazilButtons = BrazilButtons;
