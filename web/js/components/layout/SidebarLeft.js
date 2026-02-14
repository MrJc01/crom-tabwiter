
// Dependencies from window
const { Terminal: TerminalSL, Home: HomeSL, User: UserSL, X: XSL } = window;
const { IconLogIn: IconLogInSL, IconUserPlus: IconUserPlusSL } = window;

const SidebarLeft = ({ showLeftSidebar, setShowLeftSidebar, view, setView, setShowUniverses }) => {
    const handleNavigation = (targetView) => {
        if (targetView === 'login') {
            window.location.href = window.INITIAL_DATA.urls.login;
            return;
        }
        if (targetView === 'profile' && window.INITIAL_DATA.isGuest) {
            window.location.href = window.INITIAL_DATA.urls.login;
            return;
        }

        setView(targetView);
        setShowLeftSidebar(false);
        setShowUniverses(false);
    };

    return (
        <>
            {showLeftSidebar && (
                <div
                    className="fixed inset-0 bg-black/20 z-40"
                    onClick={() => setShowLeftSidebar(false)}
                ></div>
            )}
            <aside className={`
                fixed left-0 top-0 h-screen bg-white border-r border-tab-border z-50 transition-transform duration-300
                ${showLeftSidebar ? 'translate-x-0' : '-translate-x-full'} 
                w-64
                overflow-hidden
            `}>
                <div className="p-6 h-full flex flex-col w-64">
                    <div className="flex items-center gap-3 mb-8">
                        {TerminalSL && <TerminalSL size={24} />}
                        <span className="font-bold text-xl">TabWiter</span>
                    </div>
                    <nav className="flex flex-col gap-2">
                        <div onClick={() => handleNavigation('feed')} className={`flex items-center gap-4 p-3 rounded-lg cursor-pointer ${view === 'feed' ? 'bg-stone-100 font-bold' : 'hover:bg-stone-50 text-tab-muted'}`}>
                            {HomeSL && <HomeSL size={20} />} <span className="text-base">Home</span>
                        </div>
                        <div onClick={() => handleNavigation('profile')} className={`flex items-center gap-4 p-3 rounded-lg cursor-pointer ${view === 'profile' ? 'bg-stone-100 font-bold' : 'hover:bg-stone-50 text-tab-muted'}`}>
                            {UserSL && <UserSL size={20} />} <span className="text-base">Perfil</span>
                        </div>
                        <div className="mt-auto pt-4 border-t border-stone-100">
                            {window.INITIAL_DATA.isGuest ? (
                                <div className="flex flex-col gap-2">
                                    <a href={window.INITIAL_DATA.urls.login} className="flex items-center gap-4 p-3 rounded-lg cursor-pointer hover:bg-stone-50 text-tab-text">
                                        <IconLogInSL /> <span className="text-base font-bold">Entrar</span>
                                    </a>
                                    <a href={window.INITIAL_DATA.urls.signup} className="flex items-center gap-4 p-3 rounded-lg cursor-pointer hover:bg-stone-50 text-tab-accent">
                                        <IconUserPlusSL /> <span className="text-base font-bold">Criar Conta</span>
                                    </a>
                                </div>
                            ) : (
                                <a href={window.INITIAL_DATA.urls.logout} data-method="post" className={`flex items-center gap-4 p-3 rounded-lg cursor-pointer hover:bg-red-50 text-red-600`}>
                                    {XSL && <XSL size={20} />} <span className="text-base">Sair</span>
                                </a>
                            )}
                        </div>
                    </nav>
                </div>
            </aside>
        </>
    );
};
window.SidebarLeft = SidebarLeft;
