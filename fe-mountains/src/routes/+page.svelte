<script lang="ts">
    import { onMount } from 'svelte';
    import { API_URL, BASE_URL, apiFetch } from '$lib/api';
    import { goto } from '$app/navigation';
  
    type Mountain = {
      id: number;
      name: string;
      location: string;
      height: string;
      type: string;
      status: string;
      difficulty: string;
      routes: string;
      description: string | null;
      image_url: string | null;
      created_at: string;
      updated_at: string;
    };
  
    let mountains: Mountain[] = [];
    let loading = true;
    let error: string | null = null;
    let deleting: number | null = null; // Track which item is being deleted
  
    async function fetchMountains() {
      loading = true;
      error = null;
      try {
        const response = await apiFetch('/mountains');
  
        // ✅ Pastikan response punya key "data"
        if (Array.isArray(response.data)) {
          mountains = response.data;
        } else {
          error = 'Data tidak valid dari server.';
        }
      } catch (e) {
        console.error('Fetch error:', e);
        error = 'Gagal memuat data gunung.';
      } finally {
        loading = false;
      }
    }
  
    async function deleteMountain(id: number) {
      if (confirm('Yakin ingin menghapus gunung ini?')) {
        deleting = id; // Set loading state untuk item ini
        try {
          const response = await apiFetch(`/mountains/${id}`, { method: 'DELETE' });
          
          if (response.success !== false) {
            // ✅ Update state lokal langsung (optimistic update)
            mountains = mountains.filter((m) => m.id !== id);
            
            // ✅ Show success message dengan toast-style
            showToast('Gunung berhasil dihapus!', 'success');
          } else {
            showToast(response.message || 'Gagal menghapus gunung.', 'error');
          }
        } catch (err) {
          console.error('Delete error:', err);
          showToast('Gagal menghapus gunung.', 'error');
          
          // ✅ Jika error, refresh data untuk sinkronisasi
          await fetchMountains();
        } finally {
          deleting = null; // Reset loading state
        }
      }
    }
  
    // ✅ Toast notification system
    let toastMessage = '';
    let toastType: 'success' | 'error' = 'success';
    let showToastMessage = false;
  
    function showToast(message: string, type: 'success' | 'error' = 'success') {
      toastMessage = message;
      toastType = type;
      showToastMessage = true;
      
      // Auto hide after 3 seconds
      setTimeout(() => {
        showToastMessage = false;
      }, 1000);
    }
  
    // ✅ Refresh function untuk manual refresh
    async function refreshData() {
      await fetchMountains();
      showToast('Data berhasil diperbarui!', 'success');
    }
  
    onMount(fetchMountains);
  </script>
  
  <!-- Toast Notification -->
  {#if showToastMessage}
    <div class="fixed top-4 right-4 z-50 transition-all duration-300">
      <div class="px-4 py-3 rounded-lg shadow-lg {toastType === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white">
        <p>{toastMessage}</p>
      </div>
    </div>
  {/if}
  
  <div class="max-w-5xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Daftar Gunung</h1>
      <div class="flex space-x-3">
        <!-- Refresh Button -->
        <button
          on:click={refreshData}
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 cursor-pointer flex items-center"
          disabled={loading}
        >
          {#if loading}
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          {/if}
          Refresh
        </button>
        
        <!-- Add Button -->
        <button
          on:click={() => goto('/tambah')}
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 cursor-pointer"
        >
          Tambah Gunung
        </button>
      </div>
    </div>
  
    {#if loading}
      <div class="flex justify-center items-center py-8">
        <div class="flex items-center">
          <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="text-lg">Loading...</p>
        </div>
      </div>
    {:else if error}
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <p>{error}</p>
        <button
          on:click={fetchMountains}
          class="mt-2 bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700"
        >
          Coba Lagi
        </button>
      </div>
    {:else if mountains.length === 0}
      <div class="text-center py-8">
        <p class="text-gray-500 text-lg">Tidak ada data gunung.</p>
        <button
          on:click={() => goto('/tambah')}
          class="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
        >
          Tambah Gunung Pertama
        </button>
      </div>
    {:else}
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        {#each mountains as mountain}
          <div class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow {deleting === mountain.id ? 'opacity-50' : ''}">
            <!-- Image dengan fallback -->
            <div class="relative">
              <img
                src={mountain.image_url ? `${mountain.image_url}` : 'https://via.placeholder.com/300x200?text=No+Image'}
                alt={mountain.name}
                class="w-full h-48 object-cover rounded mb-3"
              />
              
              <!-- Loading overlay untuk delete -->
              {#if deleting === mountain.id}
                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded">
                  <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </div>
              {/if}
            </div>
            
            <h2 class="text-xl font-semibold mb-1">{mountain.name}</h2>
            <p class="text-gray-600 mb-1">{mountain.location}</p>
            <p class="text-sm text-gray-500 mb-1">Tinggi: {mountain.height} mdpl</p>
            <p class="text-sm text-gray-500 mb-3">Tingkat: {mountain.difficulty}</p>
  
            <div class="flex justify-between items-center">
              <a
                href={`/gunung/${mountain.id}`}
                class="text-green-700 hover:underline font-medium"
              >
                Detail
              </a>
              <div class="space-x-2">
                <button
                  on:click={() => goto(`/edit/${mountain.id}`)}
                  class="text-blue-600 hover:underline cursor-pointer"
                  disabled={deleting === mountain.id}
                >
                  Edit
                </button>
                <button
                  on:click={() => deleteMountain(mountain.id)}
                  class="text-red-600 hover:underline cursor-pointer {deleting === mountain.id ? 'opacity-50 cursor-not-allowed' : ''}"
                  disabled={deleting === mountain.id}
                >
                  {deleting === mountain.id ? 'Menghapus...' : 'Hapus'}
                </button>
              </div>
            </div>
          </div>
        {/each}
      </div>
    {/if}
  </div>