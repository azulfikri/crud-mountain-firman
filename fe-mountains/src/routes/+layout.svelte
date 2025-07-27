<script lang="ts">
    import { page } from '$app/stores';
    import '../app.css'
    $: currentPath = $page.url.pathname;
  </script>
  
  <nav class="bg-green-700 text-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
      <a href="/" class="text-xl font-bold tracking-wide">🌄 GunungKu</a>
  
      <div class="space-x-4">
        <a
          href="/"
          class="hover:underline"
          class:selected={currentPath === '/'}
        >
          Beranda
        </a>
        <a
          href="/tambah"
          class="hover:underline"
          class:selected={currentPath === '/tambah'}
        >
          Tambah Gunung
        </a>
      </div>
    </div>
  </nav>
  
  <main class="min-h-screen bg-gray-50 text-gray-800">
    <slot />
  </main>
  