
const { useState } = React;

// Dependencies from window
const { MOCK_VIDEOS, VideoCard } = window;
const { X } = window; // Close icon

// --- Video Clip Tab (The Trigger) ---
const VideoClipTab = ({ onClick }) => (
    <div
        onClick={onClick}
        className="fixed left-0 top-[65%] -translate-y-1/2 z-30 cursor-pointer group"
        title="Abrir Vídeos"
    >
        <div className="
            w-3 h-24 bg-gradient-to-b from-purple-600 via-pink-500 to-red-500
            rounded-r-lg shadow-lg
            group-hover:w-6 group-hover:h-28
            transition-all duration-300 ease-out
            flex items-center justify-center overflow-hidden
            border-r border-y border-tab-border/30
        ">
            <span className="
                material-symbols-rounded text-white text-sm
                opacity-0 group-hover:opacity-100
                transition-opacity duration-300
                rotate-90
            ">play_arrow</span>
        </div>
    </div>
);
window.VideoClipTab = VideoClipTab;

// --- Video Panel (The Slider Content) ---
const VideoPanel = ({ onClose, isMaximized, toggleMaximize }) => {
    return (
        <div className="flex flex-col h-full bg-black relative shadow-2xl">
            {/* Header / Close Button */}
            <div className="absolute top-4 right-4 z-20 flex gap-2">
                {/* Maximize Button for Desktop only */}
                <button
                    onClick={toggleMaximize}
                    className="bg-black/20 hover:bg-black/50 backdrop-blur-md text-white p-2 rounded-full transition-colors hidden lg:flex"
                >
                    <span className="material-symbols-rounded text-lg">{isMaximized ? 'close_fullscreen' : 'open_in_full'}</span>
                </button>
                <button
                    onClick={onClose}
                    className="bg-black/20 hover:bg-black/50 backdrop-blur-md text-white p-2 rounded-full transition-colors"
                >
                    {X && <X size={24} />}
                </button>
            </div>

            {/* Vertical Scroll Snap Container */}
            <div className="
                flex-1 h-full w-full overflow-y-scroll snap-y snap-mandatory 
                hide-scrollbar relative
            ">
                {MOCK_VIDEOS.map((video, index) => (
                    <div key={video.id} className="w-full h-full snap-start flex-shrink-0 relative">
                        <VideoCard video={video} isActive={true} />
                    </div>
                ))}
            </div>
        </div>
    );
};
window.VideoPanel = VideoPanel;

// --- Main Dock Component (Wrapper for floating tab) ---
const VideoDock = ({ isOpen, toggleOpen, isMaximized }) => {
    if (!isOpen) {
        return <VideoClipTab onClick={toggleOpen} />;
    }
    return null; // When open, rendered inline by MainApp
};
window.VideoDock = VideoDock;
