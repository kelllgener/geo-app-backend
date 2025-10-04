<?php

namespace App\Http\Controllers;

use App\Models\IpInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class IpInfoController extends Controller
{
    protected function ipinfoUrl($path = '')
    {
        $token = config('services.ipinfo.token') ?: env('IP_INFO_TOKEN');
        $base = 'https://ipinfo.io';
        if ($path) {
            $url = "{$base}/{$path}";
        } else {
            $url = $base;
        }

        // prefer /json suffix to force JSON
        return $token ? "{$url}/geo?token={$token}" : "{$url}/geo";
    }

    public function geo()
    {
        // call ipinfo for caller's IP
        $url = $this->ipinfoUrl();
        $res = Http::get($url);

        return response()->json($res->json(), $res->status());
    }

    public function geoByIp($ip)
    {
        // sanitize: allow ipv4/ipv6 + hostname basic check
        $path = "{$ip}";
        $url = $this->ipinfoUrl($path);
        $res = Http::get($url);

        return response()->json($res->json(), $res->status());
    }

    public function history(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $user->ipInfos()->latest()->paginate(5);
    }

    public function store(Request $request, IpInfo $ipInfo)
    {
        $validated = $request->validate([
            'ip_address' => 'required',
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $record = $ipInfo->create([
            'user_id' => $user->id,
            'ip_address' => $validated['ip_address'],
        ]);

        return response()->json([
            'message' => 'Ip address saved to history.',
            'record' => $record,
        ]);
    }

    public function destroyMultiple(Request $request) {
        $ids = $request->ids;
        if (! is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'No IDs provided.'], 400);
        }

        $deleted = IpInfo::where('user_id', Auth::id())
            ->whereIn('id', $ids)
            ->delete();

        return response()->json([
            'message' => 'Selected IP records deleted successfully.',
            'deleted_count' => $deleted
        ]);
    }
}
