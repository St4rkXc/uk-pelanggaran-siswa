<?php
/**
 * @var int $page Halaman saat ini.
 * @var int $total_pages Total jumlah halaman.
 * @var int $total_rows Total jumlah data.
 * @var int $limit Batas data per halaman.
 * @var int $offset Pergeseran data.
 */
if ($total_pages > 1): ?>
    <!-- Pagination UI (Reusable Component) -->
    <div class="flex items-center justify-between mt-8 pt-6 border-t border-zinc-200">
        <!-- Ringkasan jumlah data yang tampil -->
        <div class="text-sm text-zinc-500">
            Menampilkan <span class="font-semibold text-zinc-800"><?= min($offset + 1, $total_rows) ?></span> 
            sampai <span class="font-semibold text-zinc-800"><?= min($offset + $limit, $total_rows) ?></span> 
            dari <span class="font-semibold text-zinc-800"><?= $total_rows ?></span> data
        </div>
        
        <div class="flex items-center gap-2">
            <!-- Tombol Previous -->
            <?php if ($page > 1): 
                $prev_params = $_GET;
                $prev_params['page'] = $page - 1;
            ?>
                <a href="?<?= http_build_query($prev_params) ?>" 
                    class="p-2 rounded-lg border border-zinc-300 hover:bg-zinc-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-600"><path d="m15 18-6-6 6-6"/></svg>
                </a>
            <?php else: ?>
                <div class="p-2 rounded-lg border border-zinc-200 opacity-50 cursor-not-allowed bg-zinc-50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400"><path d="m15 18-6-6 6-6"/></svg>
                </div>
            <?php endif; ?>

            <!-- Angka Halaman (Sliding Window) -->
            <div class="flex items-center gap-1">
                <?php 
                $start_page = max(1, $page - 1);
                $end_page = min($total_pages, $page + 1);
                
                if ($start_page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="px-4 py-2 rounded-lg text-sm font-medium border border-zinc-300 hover:bg-zinc-100 transition-colors">1</a>
                    <?php if ($start_page > 2): ?>
                        <span class="px-2 text-zinc-400">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $page == $i ? 'bg-zinc-900 text-white shadow-sm' : 'text-zinc-600 hover:bg-zinc-100 border border-zinc-300' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <span class="px-2 text-zinc-400">...</span>
                    <?php endif; ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" class="px-4 py-2 rounded-lg text-sm font-medium border border-zinc-300 hover:bg-zinc-100 transition-colors"><?= $total_pages ?></a>
                <?php endif; ?>
            </div>

            <!-- Tombol Next -->
            <?php if ($page < $total_pages): 
                $next_params = $_GET;
                $next_params['page'] = $page + 1;
            ?>
                <a href="?<?= http_build_query($next_params) ?>" 
                    class="p-2 rounded-lg border border-zinc-300 hover:bg-zinc-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-600"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            <?php else: ?>
                <div class="p-2 rounded-lg border border-zinc-200 opacity-50 cursor-not-allowed bg-zinc-50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400"><path d="m9 18 6-6-6-6"/></svg>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
