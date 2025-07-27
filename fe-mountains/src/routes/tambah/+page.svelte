<script lang="ts">
    import { apiFetch } from '$lib/api';
    import { goto } from '$app/navigation';
  
    type FormGunung = {
  name: string;
  location: string;
  height: string;
  type: string;
  status: string;
  difficulty: string;
  routes: string;
  description: string;
  image: File | null;
};

let form: FormGunung = {
  name: '', location: '', height: '', type: '',
  status: '', difficulty: '', routes: '', description: '',
  image: null,
};
  
    async function handleSubmit() {
      const body = new FormData();
      for (const key in form) {
  const k = key as keyof typeof form;
  if (form[k]) body.append(k, form[k]);
}
  
      await apiFetch('/mountains', {
        method: 'POST',
        body
      });
  
      alert("Gunung berhasil ditambahkan!");
      goto('/');
    }
  </script>
  
  <div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Tambah Gunung</h1>
    
    <div class="grid gap-4 border border-gray-900 rounded px-3 py-2">
      <input class="border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.name} placeholder="Nama Gunung" required />
      <input class="border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.location} placeholder="Lokasi"  required />
      <input class="border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.height} placeholder="Tinggi (mdpl)" required />
      <input class="border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.type} placeholder="Tipe" required />
      <input class="border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.status} placeholder="Status" required />
      <input class="border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.difficulty} placeholder="Tingkat Kesulitan" required />
      <textarea class="border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.routes} placeholder="Rute Pendakian (dipisahkan koma)" required></textarea>
      <textarea class="border border-gray-900 rounded px-3 py-2 w-full" bind:value={form.description} placeholder="Deskripsi"></textarea>
      <input
      class="border border-gray-900 rounded px-3 py-2 w-full"
      type="file"
      accept="image/*"
      on:change={(e) => {
        const target = e.target as HTMLInputElement;
        if (target && target.files && target.files.length > 0) {
          form.image = target.files[0];
        }
      }}
    />  
      <button on:click={handleSubmit} class="bg-blue-600 text-white py-2 rounded hover:bg-blue-700 cursor-pointer">Simpan</button>
    </div>
  </div>
  
  <!-- <style>
    .input {
      @apply border border-gray-300 rounded px-3 py-2 w-full;
    }
  </style>
   -->