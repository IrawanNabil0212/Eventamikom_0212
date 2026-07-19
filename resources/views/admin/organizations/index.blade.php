@extends('layouts.admin')
@section('page_title', 'Kelola Penyelenggara')
@section('page_subtitle', 'Tinjau dan setujui pendaftaran organisasi/kepanitiaan baru.')
@section('content')
<div>
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    {{-- Tab Filter Status --}}
    <div class="flex gap-2 mb-6">
        @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $key => $label)
        <a href="{{ route('admin.organizations.index', ['status' => $key]) }}"
           class="px-4 py-2 rounded-lg text-sm font-bold
                  {{ $status === $key ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-6 py-4">Organisasi</th>
                    <th class="px-6 py-4">Penanggung Jawab</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($organizations as $org)
                <tr>
                    <td class="px-6 py-4">
                        <p class="font-bold">{{ $org->name }}</p>
                        <p class="text-xs text-slate-400">{{ $org->phone }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $org->owner->name ?? '-' }}<br>
                        <span class="text-slate-400">{{ $org->owner->email ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            {{ $org->status === 'approved' ? 'bg-green-100 text-green-700' :
                               ($org->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($org->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            @if($org->status !== 'approved')
                            <form action="{{ route('admin.organizations.approve', $org->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-bold hover:bg-green-700">
                                    Setujui
                                </button>
                            </form>
                            @endif

                            @if($org->status !== 'rejected')
                            <button type="button"
                                    onclick="document.getElementById('reject-modal-{{ $org->id }}').classList.remove('hidden')"
                                    class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-bold hover:bg-red-200">
                                Tolak
                            </button>
                            @endif
                        </div>

                        {{-- Modal sederhana untuk isi alasan penolakan --}}
                        <div id="reject-modal-{{ $org->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-6">
                            <div class="bg-white rounded-2xl p-6 w-full max-w-sm">
                                <h3 class="font-bold mb-4">Tolak "{{ $org->name }}"</h3>
                                <form action="{{ route('admin.organizations.reject', $org->id) }}" method="POST">
                                    @csrf
                                    <textarea name="rejection_reason" rows="3" required placeholder="Alasan penolakan..."
                                        class="w-full border-2 border-slate-100 rounded-xl p-3 mb-4"></textarea>
                                    <div class="flex gap-2">
                                        <button type="button"
                                                onclick="document.getElementById('reject-modal-{{ $org->id }}').classList.add('hidden')"
                                                class="flex-1 py-2 bg-slate-100 rounded-lg font-bold">Batal</button>
                                        <button type="submit" class="flex-1 py-2 bg-red-600 text-white rounded-lg font-bold">Tolak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $organizations->links() }}
    </div>
</div>
@endsection