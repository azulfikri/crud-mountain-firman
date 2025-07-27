<!-- <script lang="ts">
    import { onMount } from 'svelte';
    import { page } from '$app/stores';
    import { goto } from '$app/navigation';
    import { apiFetch, API_URL } from '$lib/api';
    import { error } from '@sveltejs/kit';
  
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
  
    let form: Omit<Mountain, 'id' | 'created_at' | 'updated_at'> & { image: File | null } = {
      name: '',
      location: '',
      height: '',
      type: '',
      status: '',
      difficulty: '',
      routes: '',
      description: '',
      image_url: null,
      image: null,
    };
  
    let id = $page.params.id;
  
    onMount(async () => {
      try {
        const data: Mountain = await apiFetch(`/mountains/${id}`);
        Object.assign(form, data);
      } catch (err) {
        throw error(404, 'Gunung tidak ditemukan');
      }
    });
  
    async function handleUpdate() {
      const body = new FormData();
      for (const key in form) {
        const k = key as keyof typeof form;
        if (k !== 'image_url' && form[k]) body.append(k, form[k]);
      }
  
      if (form.image) {
        body.append('image', form.image);
      }
  
      await apiFetch(`/mountains/${id}`, {
        method: 'POST', // Laravel doesn't support PUT via form-data, so override via _method
        body: (() => {
          body.append('_method', 'PUT');
          return body;
        })()
      });
  
      alert('Gunung berhasil diupdate!');
      goto('/');
    }
  </script>
  
  <div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Gunung</h1>
  
    <div class="grid gap-4 border border-gray-900 rounded px-3 py-2">
      <input class="input border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.name} placeholder="Nama Gunung" required />
      <input class="input border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.location} placeholder="Lokasi" required />
      <input class="input border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.height} placeholder="Tinggi (mdpl)" required />
      <input class="input border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.type} placeholder="Tipe" required />
      <input class="input border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.status} placeholder="Status" required />
      <input class="input border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.difficulty} placeholder="Tingkat Kesulitan" required />
      <textarea class="input border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.routes} placeholder="Rute Pendakian" required></textarea>
      <textarea class="input border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.description} placeholder="Deskripsi"></textarea>
  
      <div>
        <p class="text-sm mb-1">Gambar saat ini:</p>
        {#if form.image_url}
          <img src={`${API_URL}/${form.image_url}`} alt="Gunung" class="h-40 object-cover rounded mb-2" />
        {/if}
        <input
        class="input border border-gray-900 rounded px-3 py-2 w-full"
          type="file"
          accept="image/*"
          on:change={(e) => {
            const target = e.target as HTMLInputElement;
            if (target?.files?.[0]) {
              form.image = target.files[0];
            }
          }}
        />
      </div>
  
      <button on:click={handleUpdate} class="bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
        Simpan Perubahan
      </button>
    </div>
  </div> -->





  <script lang="ts">
    import { onMount } from 'svelte';
    import { page } from '$app/stores';
    import { goto } from '$app/navigation';
    import { apiFetch, API_URL, BASE_URL } from '$lib/api';
    import { error } from '@sveltejs/kit';
  
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
  
    let form: Omit<Mountain, 'id' | 'created_at' | 'updated_at'> & { image: File | null } = {
      name: '',
      location: '',
      height: '',
      type: '',
      status: '',
      difficulty: '',
      routes: '',
      description: '',
      image_url: null,
      image: null,
    };
  
    let id = $page.params.id;
    let loading = true;
    let submitting = false;
    let loadError = '';
  
    // Toast notification
    let toastMessage = '';
    let toastType: 'success' | 'error' = 'success';
    let showToast = false;
  
    function showToastMessage(message: string, type: 'success' | 'error' = 'success') {
      toastMessage = message;
      toastType = type;
      showToast = true;
      setTimeout(() => showToast = false, 1000);
    }
  
    onMount(async () => {
      try {
        loading = true;
        const response = await apiFetch(`/mountains/${id}`);
        
        // ✅ Handle response structure dari Laravel API
        const mountainData: Mountain = response.data || response;
        
        // ✅ Populate form dengan data yang ada
        form = {
          name: mountainData.name || '',
          location: mountainData.location || '',
          height: mountainData.height || '',
          type: mountainData.type || '',
          status: mountainData.status || '',
          difficulty: mountainData.difficulty || '',
          routes: mountainData.routes || '',
          description: mountainData.description || '',
          image_url: mountainData.image_url,
          image: null, // File input always starts null
        };
        
        console.log('Form populated with:', form);
        
      } catch (err) {
        console.error('Load error:', err);
        loadError = 'Gunung tidak ditemukan atau terjadi kesalahan.';
      } finally {
        loading = false;
      }
    });
  
    async function handleUpdate() {
      if (submitting) return;
      
      // ✅ Basic validation
      if (!form.name.trim() || !form.location.trim() || !form.height.trim()) {
        showToastMessage('Mohon isi semua field yang wajib!', 'error');
        return;
      }
  
      submitting = true;
      
      try {
        // ✅ Prepare FormData for file upload
        const formData = new FormData();
        
        // Add all form fields (except image and image_url)
        formData.append('name', form.name.trim());
        formData.append('location', form.location.trim());
        formData.append('height', form.height.trim());
        formData.append('type', form.type.trim());
        formData.append('status', form.status.trim());
        formData.append('difficulty', form.difficulty.trim());
        formData.append('routes', form.routes.trim());
        formData.append('description', form.description?.trim() || '');
        
        // Add image if new file selected
        if (form.image) {
          formData.append('image', form.image);
        }
        
        // ✅ Laravel method spoofing for PUT with FormData
        formData.append('_method', 'PUT');
  
        const response = await apiFetch(`/mountains/${id}`, {
          method: 'POST',
          body: formData,
          headers: {
            // Don't set Content-Type, let browser set it with boundary for FormData
          }
        });
  
        showToastMessage('Gunung berhasil diupdate!', 'success');
        
        // ✅ Redirect after short delay to show toast
        setTimeout(() => {
          goto('/');
        }, 1000);
        
      } catch (err: any) {
        console.error('Update error:', err);
        showToastMessage(err.message || 'Gagal mengupdate gunung!', 'error');
      } finally {
        submitting = false;
      }
    }
  
    // ✅ Handle file input change
    function handleFileChange(event: Event) {
      const target = event.target as HTMLInputElement;
      if (target?.files?.[0]) {
        form.image = target.files[0];
        console.log('New image selected:', target.files[0].name);
      }
    }
  </script>
  
  <!-- Toast Notification -->
  {#if showToast}
    <div class="fixed top-4 right-4 z-50 transition-all duration-300">
      <div class="px-4 py-3 rounded-lg shadow-lg {toastType === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white">
        <p>{toastMessage}</p>
      </div>
    </div>
  {/if}
  
  <div class="max-w-2xl mx-auto p-6">
    <div class="flex items-center mb-6">
      <button 
        on:click={() => goto('/')}
        class="mr-4 text-gray-600 hover:text-gray-800 cursor-pointer"
      >
        ← Kembali
      </button>
      <h1 class="text-2xl font-bold">Edit Gunung</h1>
    </div>
  
    {#if loading}
      <div class="flex justify-center items-center py-8">
        <div class="flex items-center">
          <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="text-lg">Memuat data...</p>
        </div>
      </div>
    {:else if loadError}
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <p>{loadError}</p>
        <button 
          on:click={() => goto('/')}
          class="mt-2 bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700"
        >
          Kembali ke Daftar
        </button>
      </div>
    {:else}
      <form on:submit|preventDefault={handleUpdate} class="space-y-4">
        <div class="grid gap-4 border border-gray-300 rounded-lg p-4 bg-white shadow-sm">
          <!-- Name -->
          <div>
            <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Gunung *</label>
            <input 
            id="nama"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
              bind:value={form.name} 
              placeholder="Nama Gunung" 
              required 
              disabled={submitting}
            />
          </div>
  
          <!-- Location -->
          <div>
            <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi *</label>
            <input 
            id="lokasi"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
              bind:value={form.location} 
              placeholder="Lokasi" 
              required 
              disabled={submitting}
            />
          </div>
  
          <!-- Height -->
          <div>
            <label for="tinggi" class="block text-sm font-medium text-gray-700 mb-1">Tinggi (mdpl) *</label>
            <input 
            id="tinggi"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
              bind:value={form.height} 
              placeholder="Tinggi (mdpl)" 
              required 
              disabled={submitting}
            />
          </div>
  
          <!-- Type -->
          <div>
            <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
            <input 
            id="tipe"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
              bind:value={form.type} 
              placeholder="Tipe" 
              required 
              disabled={submitting}
            />
          </div>
  
          <!-- Status -->
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
            <input 
            id="status"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
              bind:value={form.status} 
              placeholder="Status" 
              required 
              disabled={submitting}
            />
          </div>
  
          <!-- Difficulty -->
          <div>
            <label for="kesulitan" class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kesulitan *</label>
            <input 
            id="kesulitan"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
              bind:value={form.difficulty} 
              placeholder="Tingkat Kesulitan" 
              required 
              disabled={submitting}
            />
          </div>
  
          <!-- Routes -->
          <div>
            <label for="rute" class="block text-sm font-medium text-gray-700 mb-1">Rute Pendakian *</label>
            <textarea 
            id="rute"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
              bind:value={form.routes} 
              placeholder="Rute Pendakian (pisahkan dengan koma)" 
              rows="3"
              required 
              disabled={submitting}
            ></textarea>
          </div>
  
          <!-- Description -->
          <div>
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea 
            id="deskripsi"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
              bind:value={form.description} 
              placeholder="Deskripsi gunung (opsional)" 
              rows="4"
              disabled={submitting}
            ></textarea>
          </div>
  
          <!-- Image -->
          <div>
            <label for="gambar" class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
            
            <!-- Current Image -->
            {#if form.image_url}
              <div class="mb-3">
                <p class="text-sm text-gray-600 mb-2">Gambar saat ini:</p>
                <img 
                id="gambar"
                  src={`${form.image_url}`} 
                  alt="Gunung {form.name}" 
                  class="h-40 object-cover rounded-lg border"
                />
              </div>
            {/if}
            
            <!-- File Input -->
            <input
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              type="file"
              accept="image/*"
              on:change={handleFileChange}
              disabled={submitting}
            />
            <p class="text-xs text-gray-500 mt-1">Upload gambar baru jika ingin mengganti</p>
          </div>
  
          <!-- Submit Button -->
          <button 
            type="submit"
            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            disabled={submitting}
          >
            {#if submitting}
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Menyimpan...
            {:else}
              Simpan Perubahan
            {/if}
          </button>
        </div>
      </form>
    {/if}
  </div>