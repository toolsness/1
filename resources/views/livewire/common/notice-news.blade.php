<section id="notice-news" class="bg-white shadow-md border border-black border-solid max-w-2xl w-full relative">
    <div class="flex overflow-hidden">
        <a
        href="#notices-content"
        wire:click="setTab('notice')"
        class="text-center bg-[#41b6e6] text-white font-bold py-2 hover:bg-[#1b98ca] transition-colors duration-300 text-sm"
        @style(['flex: 3' => $tab == 'notice', 'flex: 2' => $tab != 'notice'])
        >
        Notice
        </a>
        <a
        href="#news-content"
        wire:click="setTab('news')"
        class="text-center bg-[#ff5050] text-white font-bold py-2  hover:bg-[#e61919] transition-colors duration-300 text-sm"
        @style(['flex: 3' => $tab == 'news', 'flex: 2' => $tab != 'news'])
        >
        News
        </a>
    </div>
    <div class="p-6">
        <div id="notices-content" class="{{ $tab == 'notice' ? '' : 'hidden' }}">
            <ul class="text-sm text-gray-800 leading-relaxed space-y-3 text-left list-disc">
                @forelse($notices as $notice)
                    <li>{{ $notice->content }}</li>
                @empty
                    <li>No notices available.</li>
                @endforelse
            </ul>
        </div>
        <div id="news-content" class="{{ $tab == 'news' ? '' : 'hidden' }}">
            <ul class="text-sm text-gray-800 leading-relaxed space-y-3 text-left list-disc">
                @forelse($news as $newsItem)
                    <li>{{ $newsItem->content }}</li>
                @empty
                    <li>No news available.</li>
                @endforelse
            </ul>
        </div>
    </div>
</section>

<script>
    document.addEventListener('livewire:initialized', () => {
        let lastScrollPosition = 0;

        Livewire.on('content-changed', (event) => {
            setTimeout(() => {
                window.scrollTo(0, lastScrollPosition);
            }, 100);
        });

        window.addEventListener('scroll', () => {
            lastScrollPosition = window.pageYOffset;
        });
    });
</script>
