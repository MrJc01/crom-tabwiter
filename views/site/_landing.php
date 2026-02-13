<?php
use yii\helpers\Url;
?>
<div class="bg-white min-h-screen flex items-center justify-center relative">
    <!-- Hero Section -->
    <div class="relative overflow-hidden w-full">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-center">
                <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                    <h1
                        class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl">
                        <span class="block">O que está acontecendo</span>
                        <span class="block text-brand-600">agora no mundo tech?</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-l md:mt-5 md:text-xl lg:mx-0">
                        TabWiter é a rede social definitiva para desenvolvedores. Compartilhe snippets, discuta
                        arquitetura e conecte-se com a elite da programação.
                    </p>
                    <div class="mt-8 sm:max-w-lg sm:mx-auto sm:text-center lg:text-left lg:mx-0">
                        <div class="space-y-4">
                            <a href="<?= Url::to(['site/signup']) ?>"
                                class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-brand-600 hover:bg-brand-700 md:py-4 md:text-lg md:px-10 shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                                Começar agora
                            </a>
                            <p class="text-sm text-gray-500">
                                Já tem uma conta? <a href="<?= Url::to(['site/login']) ?>"
                                    class="font-medium text-brand-600 hover:text-brand-500">Entrar</a>
                            </p>
                            <div class="pt-8 animate-bounce text-center lg:text-left">
                                <p class="text-slate-400 text-sm">Role para explorar <i
                                        class="fas fa-chevron-down ml-1"></i></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="mt-12 relative sm:max-w-lg sm:mx-auto lg:mt-0 lg:max-w-none lg:mx-0 lg:col-span-6 lg:flex lg:items-center">
                    <div class="relative mx-auto w-full rounded-lg shadow-lg lg:max-w-md overflow-hidden">
                        <div class="relative block w-full bg-white rounded-lg overflow-hidden">
                            <img class="w-full"
                                src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1632&q=80"
                                alt="">
                            <div
                                class="absolute inset-0 w-full h-full bg-gradient-to-t from-black/60 to-transparent flex items-end">
                                <div class="p-6 text-white pb-12">
                                    <p class="font-bold text-xl">Comunidade Vibrante</p>
                                    <p class="text-sm opacity-90">Milhares de devs compartilhando conhecimento
                                        diariamente.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>