<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeviceRequest;
use App\Models\Device;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(): View
    {
        $devices = Device::where('user_id', auth()->id())->latest()->get();

        return view('devices.index', compact('devices'));
    }

    public function create(): View
    {
        return view('devices.create');
    }

    public function store(DeviceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        Device::create($data);

        return redirect()->route('devices.index')->with('success', 'Perangkat berhasil ditambahkan.');
    }

    public function edit(Device $device): View
    {
        $this->authorizeDevice($device);

        return view('devices.edit', compact('device'));
    }

    public function update(DeviceRequest $request, Device $device): RedirectResponse
    {
        $this->authorizeDevice($device);

        $device->update($request->validated());

        return redirect()->route('devices.index')->with('success', 'Perangkat berhasil diperbarui.');
    }

    public function destroy(Device $device): RedirectResponse
    {
        $this->authorizeDevice($device);

        $device->delete();

        return redirect()->route('devices.index')->with('success', 'Perangkat berhasil dihapus.');
    }

    private function authorizeDevice(Device $device): void
    {
        if ($device->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
