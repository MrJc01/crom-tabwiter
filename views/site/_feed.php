<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\Post[] $posts */
?>

<div x-data="feed()" class="max-w-2xl mx-auto">

    <!-- Post Creation Form -->
    <?php if (!Yii::$app->user->isGuest): ?>
        <div class="bg-white border-b border-slate-200 px-4 py-4 sm:rounded-b-xl shadow-sm mb-6">
            <form @submit.prevent="submitPost">
                <div class="flex space-x-4">
                    <div class="flex-shrink-0">
                        <!-- User Avatar Placeholder -->
                        <div
                            class="h-10 w-10 rounded-full bg-brand-500 flex items-center justify-center text-white font-bold">
                            <?= strtoupper(substr(Yii::$app->user->identity->username, 0, 1)) ?>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="border-b border-gray-200 focus-within:border-brand-500 transition-colors">
                            <textarea x-model="content" rows="3" name="content"
                                class="block w-full border-0 border-b border-transparent p-0 pb-2 resize-none focus:ring-0 sm:text-lg placeholder-slate-400"
                                placeholder="O que está acontecendo?"></textarea>
                        </div>
                        <div class="pt-2 flex justify-between">
                            <div class="flex items-center space-x-5">
                                <!-- Icons (Image, Poll, etc.) - Visual only for now -->
                                <button type="button"
                                    class="-ml-2 -my-2 rounded-full px-3 py-2 inline-flex items-center text-left text-gray-400 group">
                                    <i class="far fa-image text-lg group-hover:text-brand-500"></i>
                                </button>
                                <button type="button"
                                    class="-ml-2 -my-2 rounded-full px-3 py-2 inline-flex items-center text-left text-gray-400 group">
                                    <i class="fas fa-poll-h text-lg group-hover:text-brand-500"></i>
                                </button>
                                <button type="button"
                                    class="-ml-2 -my-2 rounded-full px-3 py-2 inline-flex items-center text-left text-gray-400 group">
                                    <i class="far fa-smile text-lg group-hover:text-brand-500"></i>
                                </button>
                            </div>
                            <div class="flex-shrink-0">
                                <button type="submit" :disabled="!content.trim() || loading"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                    <span x-show="!loading">Tabetar</span>
                                    <span x-show="loading" class="animate-spin"><i class="fas fa-circle-notch"></i></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Feed Stream -->
    <div class="space-y-4 pb-20">
        <!-- New Posts from JS -->
        <template x-for="post in newPosts" :key="post.id">
            <div
                class="bg-white px-4 py-3 border border-slate-200 sm:rounded-xl hover:bg-slate-50 transition duration-150 cursor-pointer">
                <div class="flex space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-brand-500 flex items-center justify-center text-white font-bold"
                            x-text="post.username.charAt(0).toUpperCase()"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-slate-900">
                            <span class="hover:underline" x-text="post.username"></span>
                            <span class="text-slate-500 font-normal" x-text="' · ' + post.created_at"></span>
                        </div>
                        <div class="mt-1 text-slate-800 text-base" x-text="post.content"></div>
                        <div class="mt-2 flex items-center space-x-8 text-slate-500 text-sm">
                            <span class="inline-flex items-center space-x-2 text-brand-600">
                                <i class="fas fa-star"></i>
                                <span>0</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Server Rendered Posts -->
        <?php foreach ($posts as $post): ?>
            <div id="post-<?= $post->id ?>"
                class="bg-white px-4 py-5 border-b border-slate-200 sm:border sm:rounded-xl sm:shadow-sm hover:bg-slate-50 transition duration-150 cursor-pointer">
                <div class="flex space-x-3">
                    <div class="flex-shrink-0">
                        <div
                            class="h-10 w-10 rounded-full bg-slate-400 flex items-center justify-center text-white font-bold">
                            <?= strtoupper(substr($post->user->username, 0, 1)) ?>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-slate-900">
                            <a href="#" class="hover:underline">
                                <?= Html::encode($post->user->username) ?>
                            </a>
                            <span class="text-slate-500 font-normal"> ·
                                <?= Yii::$app->formatter->asRelativeTime($post->created_at) ?>
                            </span>
                        </div>
                        <div class="mt-2 text-slate-900 text-lg leading-snug break-words">
                            <?= \app\models\Post::formatContent($post->content) ?>
                        </div>

                        <!-- Actions -->
                        <div class="mt-3 flex justify-between max-w-md">
                            <?php if (Yii::$app->user->isGuest): ?>
                                <a href="<?= Url::to(['site/login']) ?>"
                                    class="group flex items-center space-x-2 text-slate-500 hover:text-green-500 transition focus:outline-none"
                                    title="Faça login para dar TabCoin">
                                    <div class="p-2 rounded-full group-hover:bg-green-50 transition">
                                        <i class="fas fa-chevron-up text-lg"></i>
                                    </div>
                                    <span class="vote-count group-hover:text-green-600 font-medium">
                                        <?= $post->points ?? 0 ?>
                                    </span>
                                </a>
                            <?php else: ?>
                                <button @click="vote(<?= $post->id ?>, $el)"
                                    class="group flex items-center space-x-2 text-slate-500 hover:text-green-500 transition focus:outline-none"
                                    title="Dar TabCoin (Custa 1 Mana)">
                                    <div class="p-2 rounded-full group-hover:bg-green-50 transition">
                                        <i class="fas fa-chevron-up text-lg"></i>
                                    </div>
                                    <span class="vote-count group-hover:text-green-600 font-medium">
                                        <?= $post->points ?? 0 ?>
                                    </span>
                                </button>
                            <?php endif; ?>

                            <button class="group flex items-center space-x-2 text-slate-500 hover:text-blue-500 transition">
                                <div class="p-2 rounded-full group-hover:bg-blue-50 transition">
                                    <i class="far fa-comment text-lg"></i>
                                </div>
                                <span>0</span>
                            </button>

                            <button class="group flex items-center space-x-2 text-slate-500 hover:text-red-500 transition">
                                <div class="p-2 rounded-full group-hover:bg-red-50 transition">
                                    <i class="far fa-heart text-lg"></i>
                                </div>
                            </button>

                            <button
                                class="group flex items-center space-x-2 text-slate-500 hover:text-brand-500 transition">
                                <div class="p-2 rounded-full group-hover:bg-brand-50 transition">
                                    <i class="fas fa-share text-lg"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<script>
    function feed() {
        return {
            content: '',
            loading: false,
            newPosts: [],
            submitPost() {
                if (!this.content.trim()) return;
                this.loading = true;

                fetch('<?= Url::to(['site/create-post']) ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->csrfToken ?>',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        'content': this.content
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        this.loading = false;
                        if (data.success) {
                            this.content = '';
                            this.newPosts.unshift(data.post);
                        } else {
                            alert('Erro ao postar.');
                        }
                    })
                    .catch(() => {
                        this.loading = false;
                        alert('Erro de conexão.');
                    });
            },
            vote(postId, el) {
                fetch('<?= Url::to(['site/upvote']) ?>?id=' + postId, {
                    method: 'POST',
                    headers: {
                        '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->csrfToken ?>',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update vote count in DOM
                            const countEl = el.querySelector('.vote-count');
                            countEl.innerText = data.points;
                            countEl.classList.add('text-green-600');
                            el.classList.add('text-green-500');

                            // Update Mana Display
                            const manaEl = document.getElementById('user-mana-display');
                            if (manaEl) manaEl.innerText = data.newMana;
                        } else {
                            alert(data.message || 'Erro ao votar.');
                        }
                    });
            }
        }
    }
</script>