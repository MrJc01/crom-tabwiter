
const NotificationDrawer = ({ isOpen, onClose }) => {
    const [activeTab, setActiveTab] = useState('all'); // all, mentions, system

    return (
        <>
            {/* Backdrop */}
            <div
                className={`fixed inset-0 bg-black/40 backdrop-blur-sm z-[60] transition-opacity duration-300 ${isOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'}`}
                onClick={onClose}
            ></div>

            {/* Drawer */}
            <div className={`fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-3xl bg-white rounded-b-3xl shadow-2xl z-[70] transition-all duration-500 cubic-bezier(0.34, 1.56, 0.64, 1) transform origin-top ${isOpen ? 'h-[90vh] translate-y-0 opacity-100' : 'h-[90vh] -translate-y-full opacity-50'} overflow-hidden flex flex-col`}>
                <div className="p-6 border-b border-stone-100 flex items-center justify-between bg-white z-10">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-[#009c3b]">
                            {Bell && <Bell size={20} />}
                        </div>
                        <h2 className="text-2xl font-bold text-tab-text">Notificações</h2>
                    </div>
                    <button onClick={onClose} className="p-2 hover:bg-stone-100 rounded-full text-tab-muted transition-colors">{X && <X size={24} />}</button>
                </div>

                {/* Tabs */}
                <div className="flex px-6 border-b border-stone-100 bg-white z-10">
                    {['all', 'mentions', 'system'].map(tab => (
                        <button
                            key={tab}
                            onClick={() => setActiveTab(tab)}
                            className={`px-6 py-4 font-bold text-sm transition-colors border-b-2 ${activeTab === tab ? 'border-[#009c3b] text-[#009c3b]' : 'border-transparent text-tab-muted hover:text-tab-text'}`}
                        >
                            {tab === 'all' ? 'Todas' : tab === 'mentions' ? 'Menções' : 'Sistema'}
                        </button>
                    ))}
                </div>

                {/* Content */}
                <div className="flex-1 overflow-y-auto p-6 bg-stone-50 custom-scrollbar">
                    <div className="space-y-4 max-w-2xl mx-auto">
                        {/* Mock Notifications */}
                        {[1, 2, 3, 4, 5].map(i => (
                            <div key={i} className="bg-white p-4 rounded-xl border border-stone-100 shadow-sm flex gap-4 hover:border-stone-300 transition-colors animate-enter" style={{ animationDelay: `${i * 50}ms` }}>
                                <div className="w-12 h-12 rounded-full bg-blue-50 text-[#002776] flex-shrink-0 flex items-center justify-center">
                                    {i % 2 === 0 ? (MessageSquare && <MessageSquare size={20} />) : (Terminal && <Terminal size={20} />)}
                                </div>
                                <div>
                                    <p className="text-stone-800 text-sm leading-relaxed">
                                        <span className="font-bold">Sistema:</span> Bem-vindo ao novo TabWiter! Experimente o recurso Universos clicando no ícone do globo.
                                    </p>
                                    <span className="text-xs text-tab-muted font-medium mt-1 block">Há {i} horas</span>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </>
    );
};
window.NotificationDrawer = NotificationDrawer;
