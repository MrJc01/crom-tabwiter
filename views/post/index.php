<?php
/** @var yii\web\View $this */
/** @var app\models\Post[] $posts */
/** @var array $external */

use yii\helpers\Html;

// prepare local posts data for Alpine
$jsPosts = json_encode(array_map(function($p) {
    return [
        'id' => $p->id,
        'username' => $p->user->username ?? 'unknown',
        'content' => $p->content,
        'created_at' => $p->created_at,
        'points' => $p->points,
    ];
}, $posts));

?>

<div class="flex">
    <!-- left column -->
    <aside class="w-1/4 hidden lg:block">
        <div class="p-4 bg-white rounded shadow">
            <h2 class="font-semibold mb-2">Navigation</h2>
            <nav class="space-y-1">
                <a href="<?= Yii::$app->homeUrl ?>" class="block hover:underline">Feed</a>
                <a href="#" class="block hover:underline">Profile</a>
                <a href="#" class="block hover:underline">Settings</a>
            </nav>
        </div>
        <div class="mt-4 p-4 bg-white rounded shadow">
            <h2 class="font-semibold mb-2">Mana</h2>
            <p x-text="userMana"></p>
        </div>
    </aside>

    <!-- central column -->
    <main class="flex-1 px-4" x-data="feedComponent()" x-init="init()">
        <!-- TabNews bridge validation -->
        <div class="mb-4">
            <template x-if="!authHash">
                <div class="p-2 bg-yellow-100 rounded">
                    <label class="block mb-1">TabNews username:</label>
                    <input type="text" x-model="tabnewsUser" class="w-full p-2 border rounded" placeholder="e.g. joaovictor" />
                    <button @click="validateTabnews()" class="mt-2 px-3 py-1 bg-yellow-500 text-white rounded">Validate</button>
                    <p class="text-xs text-gray-600 mt-1">Your auth_hash will be stored locally.</p>
                </div>
            </template>
            <template x-if="authHash">
                <div class="text-sm text-green-600 mb-2">Authenticated (hash stored)</div>
            </template>
        </div>
        <!-- inline post form -->
        <div class="mb-4">
            <textarea x-model="newContent" class="w-full p-2 border rounded" rows="3" placeholder="Share something..."></textarea>
            <button @click="submit()" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded">Post</button>
        </div>

        <template x-for="post in posts" :key="post.id">
            <div class="bg-white p-4 rounded shadow mb-4 transition-all duration-300" :class="{'ring-2 ring-blue-400': post.expanded}" @click="post.expanded = !post.expanded">
                <div class="text-sm text-gray-500 mb-2">Posted by <span x-text="post.username"></span> at <span x-text="new Date(post.created_at * 1000).toLocaleString()"></span></div>
                <div x-html="post.html" class="whitespace-pre-wrap"></div>
                <div class="mt-2 text-xs text-gray-600">Points: <span x-text="post.points"></span></div>
            </div>
        </template>
    </main>

    <!-- right column -->
    <aside class="w-1/4 hidden xl:block">
        <div class="p-4 bg-white rounded shadow">
            <h2 class="font-semibold mb-2">Suggestions</h2>
            <ul id="suggestions" class="list-disc list-inside text-sm text-blue-600">
                <!-- will be populated by JS based on localStorage ranking -->
            </ul>
        </div>
    </aside>
</div>

<script>
// helper to wrap hashtags and build html field
function formatPost(p) {
    const escaped = (p.content || '').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    const html = escaped.replace(/#(\w+)/g, '<span class="tag">#$1</span>');
    return Object.assign({}, p, {html});
}

function feedComponent() {
    return {
        posts: <?= $jsPosts ?>.map(formatPost),
        newContent: '',
        authHash: localStorage.getItem('auth_hash') || '',
        tabnewsUser: '',
        userMana: 0,
        init() {
            // optionally add external posts
            <?php if (!empty($external)): ?>
            const external = <?= json_encode($external) ?>;
            external.forEach(e => this.posts.push(formatPost({
                id: 'ext-'+e.id,
                username: e.user?.username || 'tabnews',
                content: e.content || e.title || '',
                created_at: Math.floor(new Date(e.published_at || Date.now()).getTime()/1000),
                points: 0
            })));
            <?php endif ?>

            // load mana if available
            this.userMana = localStorage.getItem('mana') || 0;
        },
        validateTabnews() {
            if (!this.tabnewsUser) return;
            fetch('<?= Yii::$app->urlManager->createUrl(['auth/tabnews-validate']) ?>?username=' + encodeURIComponent(this.tabnewsUser))
                .then(r=>r.json()).then(data=>{
                    if (data.auth_hash) {
                        this.authHash = data.auth_hash;
                        localStorage.setItem('auth_hash', data.auth_hash);
                        alert('validated: ' + (data.validated ? 'yes' : 'no'));
                    } else {
                        alert('failed: ' + (data.error||'unknown'));
                    }
                });
        },
        submit() {
            if (!this.newContent.trim()) return;
            if (!this.authHash) {
                alert('You need an auth_hash to post');
                return;
            }
            fetch('<?= Yii::$app->urlManager->createUrl(['post/create']) ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({auth_hash: this.authHash, content: this.newContent})
            }).then(r => r.json()).then(data => {
                if (data.status === 'ok') {
                    this.posts.unshift(formatPost({
                        id: data.id,
                        username: 'me',
                        content: this.newContent,
                        created_at: Math.floor(Date.now()/1000),
                        points: 1,
                        expanded: false
                    }));
                    this.newContent = '';
                } else {
                    alert('Post failed: ' + (data.error || 'unknown'));
                }
            });
        }
    }
}

// tag click ranking
document.addEventListener('click', function(e){
    if (e.target.matches('.tag')) {
        const tag = e.target.textContent;
        let rank = JSON.parse(localStorage.getItem('tagRank')||'{}');
        rank[tag] = (rank[tag]||0) + 1;
        localStorage.setItem('tagRank', JSON.stringify(rank));
    }
});

// on load, redirect to include interests param if needed
(function(){
    const rank = JSON.parse(localStorage.getItem('tagRank')||'{}');
    const tags = Object.entries(rank).sort((a,b)=>b[1]-a[1]).map(t=>t[0]);
    if (tags.length && !location.search.includes('interests=')) {
        const param = encodeURIComponent(tags.slice(0,5).join(','));
        location.replace(location.pathname + '?interests=' + param);
    }
})();

// populate suggestions
(function(){
    const list = document.getElementById('suggestions');
    if(!list) return;
    const rank = JSON.parse(localStorage.getItem('tagRank')||'{}');
    Object.keys(rank).sort((a,b)=>rank[b]-rank[a]).slice(0,5).forEach(tag=>{
        const li = document.createElement('li');
        li.textContent = tag;
        list.appendChild(li);
    });
})();

</script>
