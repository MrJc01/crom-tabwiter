
// --- Data ---
// INITIAL_DATA will be injected by Yii2
window.MOCK_USER = window.INITIAL_DATA.user || {
    name: "Visitante",
    handle: "guest",
    bio: "Faça login para interagir.",
    location: "Brasil",
    website: "tabnews.com.br",
    joined: "Hoje",
    tabcoins: 0,
    verified: false,
    followers: "0",
    following: "0"
};

window.TRENDING_TABS = [
    { tag: "javascript", count: "12.5k" },
    { tag: "rust", count: "8.2k" },
    { tag: "IA", count: "5.1k" },
    { tag: "carreira", count: "3.4k" },
];

window.INITIAL_POSTS = window.INITIAL_DATA.posts || [];

// --- Mock Data for Chat ---
const MOCK_USERS = [
    { id: 1, name: 'Alice Silva', handle: 'alicesilva', avatar: null, bio: 'Frontend Developer | React & Vue', followers: 120, following: 45, tabcoins: 50, verified: false },
    { id: 2, name: 'Bob Souza', handle: 'bobsouza', avatar: null, bio: 'Backend Engineer | Go & Python', followers: 340, following: 200, tabcoins: 150, verified: true },
    { id: 3, name: 'Carol Lima', handle: 'carol_dev', avatar: null, bio: 'Fullstack JS | Node.js', followers: 560, following: 300, tabcoins: 80, verified: false },
    { id: 4, name: 'Lia Oliveira', handle: 'oliveiralia', avatar: null, bio: 'DevOps & Cloud Enthusiast.', followers: 890, following: 120, tabcoins: 450, verified: false },
    { id: 5, name: 'Lucas Santos', handle: 'lucas_s', avatar: null, bio: 'Rust e C++ developer.', followers: 210, following: 80, tabcoins: 120, verified: false },
];

window.MOCK_CHATS = [
    { id: 1, type: 'direct', user: MOCK_USERS[1], lastMessage: 'Você viu a atualização do Bun?', time: '10:30', unread: 2 },
    { id: 2, type: 'direct', user: MOCK_USERS[2], lastMessage: 'O PR foi aprovado!', time: 'Ontem', unread: 0 },
    { id: 3, type: 'direct', user: MOCK_USERS[3], lastMessage: 'Vamos marcar aquele café?', time: 'Segunda', unread: 0 },
];

window.MOCK_COMMUNITIES = [
    { id: 101, type: 'group', name: 'Rust Lang Brasil', members: 1420, lastMessage: 'Alguém indo pra RustConf?', time: '09:15', unread: 5 },
    { id: 102, type: 'group', name: 'Frontend BR', members: 3500, lastMessage: 'React 19 tá chegando...', time: '11:00', unread: 12 },
    { id: 103, type: 'group', name: 'TabNews Off-Topic', members: 800, lastMessage: 'Qual melhor cadeira ergonômica?', time: '08:45', unread: 0 },
];
