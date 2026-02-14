<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="pt-BR" class="js-focus-visible" data-js-focus-visible="" data-color-mode="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Universos e Estatísticas · TabWiter</title>
    <meta name="description" content="Mapa de interações e estatísticas do TabWiter.">

    <!-- Tailwind CSS -->
    <script src="/js/libs/tailwindcss.js"></script>

    <!-- Force Graph -->
    <script src="/js/libs/force-graph.min.js"></script>

    <!-- Chart.js -->
    <script src="/js/libs/chart.min.js"></script>

    <style>
        :root {
            --bg-color: #f6f8fa;
            --text-color: #24292f;
            --border-color: #d0d7de;
            --accent-color: #0969da;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
        }

        .header-link {
            color: #24292f;
            font-weight: 600;
            text-decoration: none;
        }

        .header-link:hover {
            color: #0969da;
        }

        .chart-container {
            position: relative;
            height: 200px;
            width: 100%;
        }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        tab: {
                            bg: '#f6f8fa',
                            card: '#ffffff',
                            border: '#d0d7de',
                            text: '#24292f',
                            muted: '#57606a',
                            link: '#0969da',
                            green: '#1a7f37'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body>
    <div class="flex flex-col min-h-screen bg-tab-bg text-tab-text">


        <main class="flex-1 p-6 max-w-7xl mx-auto w-full space-y-8">

            <!-- Section: Universes Map -->
            <section class="space-y-4">

                <p class="text-tab-muted">Visualização de usuários (emojis) e posts (hashtags) conectados por
                    interações.</p>

                <div id="graph-container"
                    class="w-full h-[600px] bg-tab-card border border-tab-border rounded-xl overflow-hidden relative shadow-sm">
                    <div
                        class="absolute top-4 left-4 bg-white/90 backdrop-blur p-3 rounded-lg border border-tab-border text-xs z-10 space-y-1 shadow-sm text-tab-text">
                        <div class="font-bold mb-2">Legenda</div>
                        <div class="flex items-center space-x-2"><span>👤</span> <span>Usuário Padrão</span></div>
                        <div class="flex items-center space-x-2"><span>🌟</span> <span>Criador</span></div>
                        <div class="flex items-center space-x-2"><span>🏢</span> <span>Empresa</span></div>
                        <hr class="border-tab-border my-1">
                        <div class="flex items-center space-x-2"><span
                                class="w-3 h-3 rounded-full bg-purple-500"></span><span>Post (#Hashtag)</span></div>
                        <hr class="border-tab-border my-1">
                        <div class="flex items-center space-x-2"><span
                                class="w-6 h-[2px] bg-green-500"></span><span>Upvote</span></div>
                        <div class="flex items-center space-x-2"><span
                                class="w-6 h-[2px] bg-red-500"></span><span>Downvote</span></div>
                        <div class="flex items-center space-x-2"><span
                                class="w-6 h-[2px] bg-blue-500"></span><span>Interação (User-User)</span></div>
                    </div>
                </div>
            </section>

            <!-- Section: Statistics -->
            <section class="space-y-8">
                <h2 class="text-xl font-bold text-tab-text">Estatísticas do Site</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Chart 1: Registrations -->
                    <div class="bg-tab-card p-4 rounded-xl border border-tab-border shadow-sm">
                        <h3 class="text-sm font-semibold text-tab-text mb-4">Novos Cadastros</h3>
                        <div class="chart-container">
                            <canvas id="chart-registrations"></canvas>
                        </div>
                    </div>

                    <!-- Chart 2: Posts -->
                    <div class="bg-tab-card p-4 rounded-xl border border-tab-border shadow-sm">
                        <h3 class="text-sm font-semibold text-tab-text mb-4">Novas Publicações</h3>
                        <div class="chart-container">
                            <canvas id="chart-posts"></canvas>
                        </div>
                    </div>

                    <!-- Chart 3: Responses -->
                    <div class="bg-tab-card p-4 rounded-xl border border-tab-border shadow-sm">
                        <h3 class="text-sm font-semibold text-tab-text mb-4">Novas Respostas</h3>
                        <div class="chart-container">
                            <canvas id="chart-responses"></canvas>
                        </div>
                    </div>

                    <!-- Chart 4: Upvotes -->
                    <div class="bg-tab-card p-4 rounded-xl border border-tab-border shadow-sm">
                        <h3 class="text-sm font-semibold text-tab-text mb-4">Novas Qualificações</h3>
                        <div class="chart-container">
                            <canvas id="chart-upvotes"></canvas>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <footer class="py-6 border-t border-tab-border text-center text-tab-muted text-sm bg-tab-bg">
            <p>&copy; <?= date('Y') ?> TabWiter. Inspirado no TabNews.</p>
        </footer>
    </div>

    <!-- Scripts -->
    <script>
        function goBack() {
            // Communicate with parent React App
            window.parent.postMessage('closeUniverses', '*');
        }

        // --- Mock Data Generation ---

        const USERS_COUNT = 30;
        const POSTS_COUNT = 8;

        const nodes = [];
        const links = [];

        // 1. Create Posts (Centers)
        for (let i = 0; i < POSTS_COUNT; i++) {
            nodes.push({
                id: `post-${i}`,
                name: `#Assunto${i + 1}`,
                type: 'post',
                val: 15 // Bigger size for posts
            });
        }

        // 2. Create Users with Types
        const userTypes = ['standard', 'creator', 'company'];
        for (let i = 0; i < USERS_COUNT; i++) {
            // Weighted random for type
            let type = 'standard';
            const rand = Math.random();
            if (rand > 0.9) type = 'company';
            else if (rand > 0.7) type = 'creator';

            let emoji = '👤';
            let color = '#3b82f6'; // Standard Blue

            if (type === 'creator') {
                emoji = '🌟'; // Gold Star
                color = '#eab308'; // Yellow
            } else if (type === 'company') {
                emoji = '🏢'; // Building
                color = '#ef4444'; // Red (or company brand color)
            }

            nodes.push({
                id: `user-${i}`,
                name: `User${i + 1}`,
                type: 'user',
                userType: type,
                emoji: emoji,
                color: color,
                val: 8
            });
        }

        // 3. Connect Users to Posts (Interactions)
        nodes.filter(n => n.type === 'user').forEach(user => {
            // Connect to 1-3 random posts significantly
            const interactions = Math.floor(Math.random() * 3) + 1;
            for (let k = 0; k < interactions; k++) {
                const targetPost = nodes.filter(n => n.type === 'post')[Math.floor(Math.random() * POSTS_COUNT)];
                links.push({
                    source: user.id,
                    target: targetPost.id,
                    type: Math.random() > 0.2 ? 'up' : 'down' // 80% upvotes
                });
            }
        });

        // 4. Connect Users to Users (Comments/Replies)
        // Create random links between users
        for (let i = 0; i < USERS_COUNT * 1.5; i++) {
            const userA = nodes.filter(n => n.type === 'user')[Math.floor(Math.random() * USERS_COUNT)];
            const userB = nodes.filter(n => n.type === 'user')[Math.floor(Math.random() * USERS_COUNT)];

            if (userA.id !== userB.id) {
                links.push({
                    source: userA.id,
                    target: userB.id,
                    type: 'interaction'
                });
            }
        }

        // --- Force Graph Initialization ---
        const elem = document.getElementById('graph-container');
        const Graph = ForceGraph()(elem)
            .backgroundColor('#ffffff') // Light background
            .graphData({ nodes, links })
            .nodeLabel('name')
            .nodeCanvasObject((node, ctx, globalScale) => {
                const label = node.name;
                const fontSize = 12 / globalScale;
                ctx.font = `${fontSize}px Sans-Serif`;
                const textWidth = ctx.measureText(label).width;
                const bckgDimensions = [textWidth, fontSize].map(n => n + fontSize * 0.2); // some padding

                if (node.type === 'post') {
                    // Draw Circle for Post
                    ctx.beginPath();
                    ctx.arc(node.x, node.y, 5, 0, 2 * Math.PI, false);
                    ctx.fillStyle = '#a855f7'; // Purple
                    ctx.fill();

                    // Draw Label (#Hashtag) ALWAYS visible
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = '#24292f'; // Dark text
                    ctx.fillText(label, node.x, node.y + 8);

                } else {
                    // Draw Emoji for User
                    const size = 10;
                    ctx.font = `${size}px Sans-Serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(node.emoji, node.x, node.y);
                }
            })
            // .nodeColor(node => node.type === 'post' ? '#a855f7' : node.color) // Superseded by custom canvas
            .linkColor(link => {
                if (link.type === 'up') return '#238636'; // Green
                if (link.type === 'down') return '#cf222e'; // Red
                return '#0969da'; // Blue for user interactions
            })
            .linkWidth(link => link.type === 'interaction' ? 1 : 2)
            .linkDirectionalParticles(2)
            .linkDirectionalParticleSpeed(d => d.value * 0.001);

        // Resize Graph on Window Resize
        window.addEventListener('resize', () => {
            Graph.width(elem.clientWidth);
            Graph.height(elem.clientHeight);
        });

        // --- Chart.js Initialization ---
        Chart.defaults.color = '#57606a'; // Muted text
        Chart.defaults.borderColor = '#d0d7de'; // Light border

        const createChart = (ctxId, label, color) => {
            const ctx = document.getElementById(ctxId).getContext('2d');
            const labels = Array.from({ length: 30 }, (_, i) => {
                const d = new Date();
                d.setDate(d.getDate() - (29 - i));
                return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
            });
            const data = Array.from({ length: 30 }, () => Math.floor(Math.random() * 50) + 10);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: color,
                        borderRadius: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#d0d7de' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        };

        createChart('chart-registrations', 'Cadastros', '#1a7f37');
        createChart('chart-posts', 'Publicações', '#1a7f37');
        createChart('chart-responses', 'Respostas', '#1a7f37');
        createChart('chart-upvotes', 'Qualificações', '#1a7f37');

    </script>
</body>

</html>