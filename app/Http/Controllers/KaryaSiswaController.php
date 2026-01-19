<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKaryaSiswaRequest;
use App\Http\Requests\UpdateKaryaSiswaRequest;
use App\Models\KaryaSiswa;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaryaSiswaController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/karya-siswa",
     *     summary="Tambah karya siswa baru",
     *     description="Create new student work with image and markdown description",
     *     operationId="storeKaryaSiswa",
     *     tags={"Karya Siswa"},
     *     security={{"bearer_token": {}}},
     *     requestBody={
     *         "required": true,
     *         "content": {
     *             "multipart/form-data": {
     *                 "schema": {
     *                     "type": "object",
     *                     "required": {"judul", "deskripsi", "kategori", "teknologi", "nama_siswa", "kelas", "tahun", "status"},
     *                     "properties": {
     *                         "judul": {"type": "string", "maxLength": 255, "description": "Judul karya"},
     *                         "deskripsi": {"type": "string", "description": "Deskripsi dalam format markdown"},
     *                         "kategori": {"type": "string", "enum": {"web", "mobile", "desktop", "game", "iot", "lainnya"}, "description": "Kategori aplikasi"},
     *                         "teknologi": {"type": "string", "maxLength": 255, "description": "Tech stack yang digunakan, misal: React, Laravel"},
     *                         "nama_siswa": {"type": "string", "maxLength": 255, "description": "Nama siswa pembuat karya"},
     *                         "kelas": {"type": "string", "maxLength": 255, "description": "Kelas siswa, misal: XII RPL 1"},
     *                         "tahun": {"type": "integer", "minimum": 2000, "description": "Tahun pembuatan karya"},
     *                         "gambar": {"type": "string", "format": "binary", "description": "Tangkapan layar karya (16:9 ratio, akan dikonversi ke WebP)"},
     *                         "url_demo": {"type": "string", "format": "url", "description": "URL demo aplikasi (opsional)"},
     *                         "url_repo": {"type": "string", "format": "url", "description": "URL repository GitHub (opsional)"},
     *                         "status": {"type": "string", "enum": {"draft", "review", "published", "archived"}, "description": "Status publikasi"}
     *                     }
     *                 }
     *             }
     *         }
     *     },
     *     @OA\Response(
     *         response=201,
     *         description="Karya siswa berhasil dibuat",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Karya siswa berhasil dibuat"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="judul", type="string"),
     *                 @OA\Property(property="deskripsi", type="string"),
     *                 @OA\Property(property="kategori", type="string"),
     *                 @OA\Property(property="teknologi", type="string"),
     *                 @OA\Property(property="nama_siswa", type="string"),
     *                 @OA\Property(property="kelas", type="string"),
     *                 @OA\Property(property="tahun", type="integer"),
     *                 @OA\Property(property="gambar", type="string"),
     *                 @OA\Property(property="url_demo", type="string"),
     *                 @OA\Property(property="url_repo", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(StoreKaryaSiswaRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();

            // Handle image upload via ImageService
            if ($request->hasFile('gambar')) {
                $imageService = app(ImageService::class);
                $data['gambar'] = $imageService->processAndStore(
                    $request->file('gambar'),
                    'karya-siswa'
                );
            }

            // Handle published_at timestamp
            if ($data['status'] === 'published') {
                $data['published_at'] = now();
            }

            $karya = KaryaSiswa::create($data);

            return response()->json([
                'message' => 'Karya siswa berhasil dibuat',
                'data' => $karya->load('user'),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat karya siswa',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/karya-siswa/published",
     *     summary="Dapatkan karya siswa yang dipublikasikan",
     *     description="Get published student works for public display (limit 6)",
     *     operationId="getPublishedKaryaSiswa",
     *     tags={"Karya Siswa - Public"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Jumlah data yang ditampilkan (default: 6)",
     *         required=false,
     *         @OA\Schema(type="integer", default=6)
     *     ),
     *     @OA\Parameter(
     *         name="kategori",
     *         in="query",
     *         description="Filter berdasarkan kategori",
     *         required=false,
     *         @OA\Schema(type="string", enum={"web", "mobile", "desktop", "game", "iot", "lainnya"})
     *     ),
     *     @OA\Parameter(
     *         name="tahun",
     *         in="query",
     *         description="Filter berdasarkan tahun",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar karya siswa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="judul", type="string"),
     *                     @OA\Property(property="deskripsi_excerpt", type="string"),
     *                     @OA\Property(property="kategori", type="string"),
     *                     @OA\Property(property="teknologi", type="string"),
     *                     @OA\Property(property="nama_siswa", type="string"),
     *                     @OA\Property(property="gambar", type="string"),
     *                     @OA\Property(property="image_url", type="string"),
     *                     @OA\Property(property="url_demo", type="string"),
     *                     @OA\Property(property="url_repo", type="string"),
     *                     @OA\Property(property="published_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="limit", type="integer")
     *         )
     *     )
     * )
     */
    public function getPublished(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 6);
        $kategori = $request->input('kategori');
        $tahun = $request->input('tahun');

        $query = KaryaSiswa::where('status', '=', 'published')
            ->orderBy('published_at', 'desc');

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        $karyas = $query->limit($limit)->get();

        return response()->json([
            'data' => $karyas->map(fn($k) => [
                'id' => $k->id,
                'judul' => $k->judul,
                'deskripsi_excerpt' => $k->deskripsi_excerpt,
                'kategori' => $k->kategori,
                'teknologi' => $k->teknologi,
                'nama_siswa' => $k->nama_siswa,
                'kelas' => $k->kelas,
                'tahun' => $k->tahun,
                'gambar' => $k->gambar,
                'image_url' => $k->image_url,
                'url_demo' => $k->url_demo,
                'url_repo' => $k->url_repo,
                'published_at' => $k->published_at,
            ]),
            'total' => KaryaSiswa::where('status', '=', 'published')->count(),
            'limit' => $limit,
        ]);
    }
}
