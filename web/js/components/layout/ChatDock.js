
// --- Chat Dock: Inline Lateral Panel ---
// The clip tab is fixed on the right edge.
// The panel content is rendered inline by MainApp, not here.
// This component handles:
//   - Closed: renders the clip tab (fixed on right edge)
//   - Open: renders the chat panel content (positioned inline by parent)

const ChatClipTab = ({ onClick }) => (
    <div
        onClick={onClick}
        className="fixed right-0 top-1/2 -translate-y-1/2 z-30 cursor-pointer group"
        title="Abrir Chat"
    >
        <div className="
            w-3 h-24 bg-gradient-to-b from-tab-accent via-emerald-500 to-teal-600
            rounded-l-lg shadow-lg
            group-hover:w-6 group-hover:h-28
            transition-all duration-300 ease-out
            flex items-center justify-center overflow-hidden
            border-l border-y border-tab-border/30
        ">
            <span className="
                material-symbols-rounded text-white text-sm
                opacity-0 group-hover:opacity-100
                transition-opacity duration-300
                -rotate-90
            ">chat</span>
        </div>
    </div>
);
window.ChatClipTab = ChatClipTab;


const ChatPanel = ({ onClose, isMaximized, toggleMaximize }) => {
    const [activeTab, setActiveTab] = useState('direct');
    const [selectedChat, setSelectedChat] = useState(null);

    const chats = activeTab === 'direct' ? MOCK_CHATS : MOCK_COMMUNITIES;

    return (
        <div className="flex flex-col h-full bg-white">
            {/* Header */}
            <div className="h-14 border-b border-stone-100 flex items-center justify-between px-4 bg-stone-50 flex-shrink-0">
                <div className="flex items-center gap-2 font-bold text-tab-text">
                    <span className="material-symbols-rounded text-tab-accent">forum</span>
                    Msg
                </div>
                <div className="flex items-center gap-1">
                    <button onClick={toggleMaximize} className="p-2 hover:bg-stone-200 rounded-lg text-tab-muted transition-colors hidden lg:flex" title={isMaximized ? "Restaurar" : "Maximizar"}>
                        <span className="material-symbols-rounded text-lg">{isMaximized ? 'close_fullscreen' : 'open_in_full'}</span>
                    </button>
                    <button onClick={() => { setSelectedChat(null); onClose(); }} className="p-2 hover:bg-stone-200 rounded-lg text-tab-muted transition-colors">
                        <span className="material-symbols-rounded text-lg">close</span>
                    </button>
                </div>
            </div>

            {/* Body */}
            <div className="flex flex-1 overflow-hidden">
                {/* Chat List */}
                <div className={`${selectedChat ? 'hidden lg:flex lg:w-72 border-r border-stone-100' : 'w-full'} flex-col bg-white transition-all duration-300 flex-shrink-0`}>
                    {/* Tabs */}
                    <div className="flex border-b border-stone-100 flex-shrink-0">
                        <button
                            onClick={() => setActiveTab('direct')}
                            className={`flex-1 py-3 text-sm font-medium transition-colors ${activeTab === 'direct' ? 'text-tab-accent border-b-2 border-tab-accent bg-green-50/50' : 'text-tab-muted hover:bg-stone-50'}`}
                        >
                            Conversas
                        </button>
                        <button
                            onClick={() => setActiveTab('communities')}
                            className={`flex-1 py-3 text-sm font-medium transition-colors ${activeTab === 'communities' ? 'text-tab-accent border-b-2 border-tab-accent bg-green-50/50' : 'text-tab-muted hover:bg-stone-50'}`}
                        >
                            Comunidades
                        </button>
                    </div>

                    {/* List */}
                    <div className="flex-1 overflow-y-auto p-2 custom-scrollbar">
                        {chats.map(chat => (
                            <div
                                key={chat.id}
                                onClick={() => setSelectedChat(chat)}
                                className={`p-3 rounded-lg flex gap-3 cursor-pointer transition-colors ${selectedChat?.id === chat.id ? 'bg-blue-50 border border-blue-100' : 'hover:bg-stone-50 border border-transparent'}`}
                            >
                                <div className={`w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center text-white font-bold ${activeTab === 'direct' ? 'bg-gradient-to-br from-blue-400 to-blue-600' : 'bg-gradient-to-br from-purple-400 to-purple-600'}`}>
                                    {activeTab === 'direct' ? chat.user.name[0] : '#'}
                                </div>
                                <div className="flex-1 min-w-0">
                                    <div className="flex justify-between items-baseline">
                                        <h4 className="font-bold text-sm truncate text-stone-800">{activeTab === 'direct' ? chat.user.name : chat.name}</h4>
                                        <span className="text-[10px] text-tab-muted">{chat.time}</span>
                                    </div>
                                    <p className="text-xs text-tab-muted truncate leading-tight mt-0.5">{chat.lastMessage}</p>
                                </div>
                                {chat.unread > 0 && (
                                    <div className="min-w-[18px] h-[18px] rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center -mt-1 -mr-1">
                                        {chat.unread}
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                </div>

                {/* Chat Window */}
                {selectedChat && (
                    <div className="flex-1 flex flex-col bg-stone-50 animate-enter min-w-0">
                        {/* Back button on mobile */}
                        <div className="lg:hidden h-12 border-b border-stone-200 bg-white flex items-center px-3 flex-shrink-0">
                            <button onClick={() => setSelectedChat(null)} className="p-1 hover:bg-stone-100 rounded-lg text-tab-muted flex items-center gap-2 text-sm font-medium">
                                <span className="material-symbols-rounded text-lg">arrow_back</span> Voltar
                            </button>
                        </div>

                        {/* Chat Header */}
                        <div className="h-14 border-b border-stone-200 bg-white flex items-center justify-between px-4 shadow-sm z-10 flex-shrink-0">
                            <div className="flex items-center gap-3">
                                <div className={`w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-white font-bold text-sm ${activeTab === 'direct' ? 'bg-blue-500' : 'bg-purple-500'}`}>
                                    {activeTab === 'direct' ? selectedChat.user.name[0] : '#'}
                                </div>
                                <div>
                                    <h3 className="font-bold text-stone-800 text-sm">{activeTab === 'direct' ? selectedChat.user.name : selectedChat.name}</h3>
                                    <span className="text-xs text-green-600 flex items-center gap-1 block leading-none">
                                        <span className="w-1.5 h-1.5 rounded-full bg-green-500"></span> Online
                                    </span>
                                </div>
                            </div>
                            <button className="p-2 hover:bg-stone-100 rounded-full text-tab-muted">
                                <MoreHorizontal size={18} />
                            </button>
                        </div>

                        {/* Messages */}
                        <div className="flex-1 overflow-y-auto p-4 space-y-4">
                            <div className="flex justify-center my-4">
                                <span className="text-xs font-medium text-tab-muted bg-stone-200 px-3 py-1 rounded-full">Hoje</span>
                            </div>
                            <div className="flex gap-3">
                                <div className="w-8 h-8 rounded-full flex-shrink-0 bg-stone-300 mt-1"></div>
                                <div className="bg-white border border-stone-200 p-3 rounded-2xl rounded-tl-none max-w-[80%] shadow-sm">
                                    <p className="text-sm text-stone-800">{selectedChat.lastMessage}</p>
                                    <span className="text-[10px] text-tab-muted mt-1 block text-right">10:30</span>
                                </div>
                            </div>
                            <div className="flex gap-3 justify-end">
                                <div className="bg-[#dafbe1] border border-green-200 p-3 rounded-2xl rounded-tr-none max-w-[80%] shadow-sm">
                                    <p className="text-sm text-stone-800">Com certeza! Vou dar uma olhada nisso agora mesmo.</p>
                                    <span className="text-[10px] text-green-700 mt-1 block text-right flex items-center justify-end gap-1">
                                        10:32 <span className="material-symbols-rounded text-[14px]">done_all</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {/* Input */}
                        <div className="p-3 bg-white border-t border-stone-200 flex gap-2 items-end flex-shrink-0">
                            <button className="p-2 text-tab-muted hover:bg-stone-100 rounded-full"><span className="material-symbols-rounded">add_circle</span></button>
                            <div className="flex-1 bg-stone-100 rounded-xl p-2 focus-within:ring-2 focus-within:ring-green-100 transition-all border border-transparent focus-within:border-green-200">
                                <textarea placeholder="Digite sua mensagem..." className="w-full bg-transparent border-none focus:ring-0 text-sm resize-none h-6 max-h-24 outline-none" rows="1"></textarea>
                            </div>
                            <button className="p-2 bg-tab-accent text-white rounded-full hover:bg-green-700 shadow-sm transition-colors">
                                <span className="material-symbols-rounded text-xl translate-x-[2px] translate-y-[1px]">send</span>
                            </button>
                        </div>
                    </div>
                )}

                {/* Empty state (desktop only, when no chat selected and list is showing) */}
                {!selectedChat && isMaximized && (
                    <div className="hidden lg:flex flex-1 flex-col items-center justify-center text-tab-muted gap-4 bg-stone-50">
                        <span className="material-symbols-rounded text-6xl text-stone-200">forum</span>
                        <p className="font-bold text-stone-400">Selecione uma conversa</p>
                        <p className="text-sm text-stone-300">Escolha uma conversa ao lado para começar</p>
                    </div>
                )}
            </div>
        </div>
    );
};
window.ChatPanel = ChatPanel;

// Keep ChatDock as a compatibility wrapper — it just renders the clip tab
const ChatDock = ({ isOpen, toggleOpen, isMaximized, toggleMaximize }) => {
    // Only render the clip tab when chat is closed
    if (!isOpen) {
        return <ChatClipTab onClick={toggleOpen} />;
    }
    // When open, the panel is rendered by MainApp inline — nothing to render here
    return null;
};
window.ChatDock = ChatDock;
