
// Dependencies from window
const { Search: SearchSR } = window;
const { TRENDING_TABS: TRENDING_TABS_SR } = window;

const SidebarRight = ({ showRightSidebar, setShowRightSidebar, onHashClick }) => {
    return (
        <>
            {showRightSidebar && (
                <div
                    className="fixed inset-0 bg-black/20 z-40"
                    onClick={() => setShowRightSidebar(false)}
                ></div>
            )}
            <aside className={`
                fixed right-0 top-0 h-screen bg-white border-l border-tab-border z-50 transition-transform duration-300
                ${showRightSidebar ? 'translate-x-0' : 'translate-x-full'} 
                w-80
                overflow-hidden
            `}>
                <div className="p-6 h-full w-80 overflow-y-auto">
                    {/* Search in Sidebar */}
                    <div className="relative group mb-6">
                        <div className="absolute left-3 top-2.5 text-tab-muted group-focus-within:text-tab-accent">
                            {SearchSR && <SearchSR size={18} />}
                        </div>
                        <input type="text" placeholder="Buscar no TabWiter" className="w-full bg-stone-50 border border-stone-200 focus:bg-white focus:border-tab-accent py-2 pl-10 pr-4 rounded-lg text-sm focus:outline-none transition-all" />
                    </div>

                    {window.INITIAL_DATA.isGuest && (
                        <div className="bg-blue-50 p-4 rounded-lg mb-6 text-center">
                            <h3 className="font-bold mb-2">Junte-se ao TabWiter</h3>
                            <div className="flex flex-col gap-2">
                                <a href={window.INITIAL_DATA.urls.signup} className="bg-tab-text text-white py-2 rounded font-bold">Criar conta</a>
                                <a href={window.INITIAL_DATA.urls.login} className="text-tab-text font-bold">Entrar</a>
                            </div>
                        </div>
                    )}

                    <h2 className="font-bold text-lg mb-4">Trending</h2>
                    <div className="flex flex-col gap-2">
                        {TRENDING_TABS_SR.map((topic, i) => (
                            <div key={i} onClick={() => onHashClick(`#${topic.tag}`)} className="flex justify-between items-center cursor-pointer hover:bg-stone-50 p-3 rounded-lg border border-stone-100 transition-colors">
                                <div className="font-bold text-tab-text">#{topic.tag}</div>
                                <div className="text-xs text-tab-muted">{topic.count}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </aside>
        </>
    );
};
window.SidebarRight = SidebarRight;
