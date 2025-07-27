export const API_URL = "http://localhost:8000/api";
export const BASE_URL = "http://localhost:8000"; // base url untuk gambar

export async function apiFetch(endpoint: string, options: RequestInit = {}) {
  const res = await fetch(`${API_URL}${endpoint}`, {
    headers: {
      Accept: "application/json",
      ...(options.headers || {}),
    },
    ...options,
  });

  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.message || "Terjadi kesalahan");
  }

  return await res.json();
}
