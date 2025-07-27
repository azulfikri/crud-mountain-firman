<script lang="ts">
    import { page } from '$app/stores';
    import { onMount } from 'svelte';
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

  let mountain: Mountain | null = null;
  
  onMount(async () => {
    const id = $page.params.id;
    try {
      mountain = await apiFetch(`/mountains/${id}`);
    } catch (err) {
      throw error(404, 'Gunung tidak ditemukan');
    }
  });
  </script>
  
  {#if mountain}
    <div class="max-w-4xl mx-auto p-6 bg-white shadow rounded mt-6">
      <img
        src={mountain.image_url ? `${mountain.image_url}` : 'https://via.placeholder.com/600x300'}
        alt={mountain.name}
        class="w-full h-100 object-center rounded mb-4"
      />
      <h1 class="text-3xl font-bold mb-2">{mountain.name}</h1>
      <p class="text-gray-600 mb-2">📍 {mountain.location}</p>
      <p class="mb-1"><strong>Tinggi:</strong> {mountain.height} mdpl</p>
      <p class="mb-1"><strong>Tipe:</strong> {mountain.type}</p>
      <p class="mb-1"><strong>Status:</strong> {mountain.status}</p>
      <p class="mb-1"><strong>Tingkat Kesulitan:</strong> {mountain.difficulty}</p>
      <p class="mb-1"><strong>Rute Pendakian:</strong> {mountain.routes}</p>
      {#if mountain.description}
        <div class="mt-4">
          <h2 class="text-xl font-semibold mb-1">Deskripsi</h2>
          <p class="text-gray-700">{mountain.description}</p>
        </div>
      {/if}
    </div>
  {:else}
    <div class="p-6 text-center text-gray-500">Memuat data gunung...</div>
  {/if}
  