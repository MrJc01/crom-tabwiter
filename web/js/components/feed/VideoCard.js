
// Dependencies
const { useState, useEffect, useRef } = React;

const VideoCard = ({ video, isActive }) => {
    // Only render iframe if active (lazy load expectation) or close to valid
    // For simplicity, we render all but could implement IntersectionObserver later if performance lags.
    // Given the TikTok slider style, we want them ready.

    return (
        <div className="w-full h-full flex-shrink-0 snap-start bg-black relative flex items-center justify-center overflow-hidden">
            {video.type === 'youtube' ? (
                <div className="absolute inset-0 w-full h-full pointer-events-auto">
                    <iframe
                        width="100%"
                        height="100%"
                        // Add origin to fix some embedding checks, enable js api
                        src={`https://www.youtube.com/embed/${video.videoId}?controls=0&rel=0&playsinline=1&modestbranding=1&iv_load_policy=3&enablejsapi=1&origin=${window.location.origin}`}
                        title={video.title}
                        frameBorder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowFullScreen
                        className="w-full h-full object-cover"
                    ></iframe>
                    {/* Overlay to prevent clicking title/avatar freely if we want TikTok style interaction only, but let's allow controls for now */}
                </div>
            ) : video.type === 'tiktok' ? (
                <div className="absolute inset-0 w-full h-full bg-black flex items-center justify-center pointer-events-auto">
                    <iframe
                        src={`https://www.tiktok.com/embed/v2/${video.videoId}`}
                        width="100%"
                        height="100%"
                        frameBorder="0"
                        allow="fullscreen"
                        className="w-full h-full"
                    >
                    </iframe>
                </div>
            ) : (
                <div className="text-white">Formato desconhecido</div>
            )}

            {/* Meta Overlay (Title, etc) - optional styling similar to TikTok */}
            <div className="absolute bottom-4 left-4 z-10 pointer-events-none">
                <h3 className="text-white font-bold drop-shadow-md text-sm">{video.title}</h3>
                <span className="text-xs text-white/80 drop-shadow-md uppercase bg-black/50 px-2 py-0.5 rounded">{video.type}</span>
            </div>
        </div>
    );
};
window.VideoCard = VideoCard;
