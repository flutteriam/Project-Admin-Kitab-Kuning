<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\PostBab;
use App\Models\Category;
use App\Models\PostBait;
use App\Models\PostKata;
use App\Models\PostKataTarqib;
use App\Models\PostKataVar;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Requests\NahwuRequest;

class TarqibController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id = null)
    {
        $categories = Category::all();
        $bab = null;
        $temp_post = null;
        $post = null;
        $first_bait = null;
        $active_category = null;
        if($id) {
            $bab = PostBab::with('post')->find($id);
            $temp_post = Post::find($bab->post_id);
            $post = Post::where('category_id', $temp_post->category_id)->get();
            $active_category = Category::find($post->first()->category_id);
            $first_bait = PostBait::where('bab_id', $bab->id)->orderBy('no', 'ASC')->first()->id;
        }
        return view('post::tarqib', compact('categories', 'bab', 'temp_post', 'post', 'active_category', 'first_bait'));
    }

    public function ajax_bab_detail($bab_id) {
        $babs = PostBab::where('id', $bab_id)->with(['bait.kata', 'bait.kata.tarqib'])->orderBy('no', 'ASC')->get();
        return view('post::ajax_bab_detail_tarqib', compact("babs"));
    }

    public function destroy($id)
    {
        try {
            //code...
            // $tarqib = PostKataTarqib::find($id);
            $tarqib = PostKataTarqib::where('kata_id',$id)->first();
            $tarqib->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menghapus Tarqib'
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    // public function store(ChapterCreateRequest $request)
    // {
    //     $last_no = PostKata::where('bab_id', $request->bab_id)->where('bait_id', $request->bait_id)->latest()->first()->no ?? 0;
    //     $request->merge([
    //         'no' => ($last_no + 1)
    //     ]);
    //     PostKata::create($request->all());
    //     return response()->json([
    //         'status' => true,
    //         'message' => [
    //             'head' => 'Berhasil',
    //             'body' => 'Menambahkan Kata baru'
    //         ]
    //     ], 200);
    // }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        // Id => id bait bukan id kata
        return response()->json([
            'status' => true,
            'data' => PostKata::where('bait_id', $id)->orderBy('no', 'ASC')->get()
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $checkTarqib = PostKataTarqib::where('kata_id', $id)->first();
        // dd($checkTarqib);

        $dataBreadcumTarqib = [];
        $dataOpt = [];
        $columnStore = '';
        $step_current = 1;
        $step_end = 2;
        if (is_null($checkTarqib)) {
            // $step_current = 0;
            // $step_end = 1;
            $step_next = 2;

            $dataOpt = $this->enumJenis();

            $columnStore = 'jenis';
        } else {
            // $step_current = 1;
            // $step_end = 2;

            $dataBreadcumTarqib[] = $checkTarqib->jenis;

            // if ($checkTarqib->jenis == 'fiil') {
            //     $step_current = 2;
            //     $step_end = 3;

            //     if (is_null($checkTarqib->jenis_fiil)) {
            //         $dataOpt = $this->enumJenisFiil();
            //     } else {
            //         $step_current = 3;
            //         $step_end = 4;

            //         $dataBreadcumTarqib[] = $checkTarqib->jenis_fiil;

            //         if ($checkTarqib->jenis_fiil == 'madi' || $checkTarqib->jenis_fiil == 'mudlori') {
            //             $step_end = 5;

            //             if (is_null($checkTarqib->fiil_mm)) {
            //                 $dataOpt = $this->enumJenisFiilMM();
            //             } else {
            //                 $step_current = 4;

            //                 $dataBreadcumTarqib[] = $checkTarqib->fiil_mm;

            //                 if (is_null($checkTarqib->fiil_mm2)) {
            //                     $dataOpt = $this->enumJenisFiilMM2();
            //                 } else {
            //                     $dataBreadcumTarqib[] = $checkTarqib->fiil_mm2;
            //                 }
            //             }
            //         } else {
            //             $step_current = 4;
            //         }
            //     }
            // }
            if ($checkTarqib->jenis == 'fiil') {
                $step_current = 2;
                $step_end = 3;
                $step_next = 3;
                if (!is_null($checkTarqib->jenis_fiil)) {
                    $step_current = 3;
                    $step_next = 4;
                    if ($checkTarqib->jenis_fiil == 'madi' || $checkTarqib->jenis_fiil == 'mudlori') {
                        $step_end = 5;
                        if (!is_null($checkTarqib->fiil_mm)) {
                            $step_current = 4;
                            $step_next = 5;
                            if (!is_null($checkTarqib->fiil_mm2)) {
                                $step_current = 5;
                                $step_next = 5;
                            }
                        }
                    } else {
                        $step_current = 3;
                        $step_next = 3;
                    }
                }
            }
            if ($checkTarqib->jenis == 'fiil') {
                if (!is_null($checkTarqib->jenis_fiil)) {
                    $dataBreadcumTarqib[] = $checkTarqib->jenis_fiil;
                    if ($checkTarqib->jenis_fiil == 'madi' || $checkTarqib->jenis_fiil == 'mudlori') {
                        if (!is_null($checkTarqib->fiil_mm)) {
                            $dataBreadcumTarqib[] = $checkTarqib->fiil_mm;
                            if (!is_null($checkTarqib->fiil_mm2)) {
                                $dataBreadcumTarqib[] = $checkTarqib->fiil_mm2;
                            }
                        }
                    }
                }
            }
            if ($checkTarqib->jenis == 'fiil') {
                if (is_null($checkTarqib->jenis_fiil)) {
                    $dataOpt = $this->enumJenisFiil();
                    $columnStore = 'jenis_fiil';
                } else {
                    if ($checkTarqib->jenis_fiil == 'madi' || $checkTarqib->jenis_fiil == 'mudlori') {
                        if (is_null($checkTarqib->fiil_mm)) {
                            $dataOpt = $this->enumJenisFiilMM();
                            $columnStore = 'fiil_mm';
                        } else {
                            if (is_null($checkTarqib->fiil_mm2)) {
                                $dataOpt = $this->enumJenisFiilMM2();
                                $columnStore = 'fiil_mm2';
                            }
                        }
                    }
                }
            }

            if ($checkTarqib->jenis == 'isim') {
                $step_current = 2;
                $step_end = 5;
                $step_next = 3;
                if (!is_null($checkTarqib->isim_mr)) {
                    $step_current = 3;
                    $step_next = 4;
                    if (!is_null($checkTarqib->isim_mm)) {
                        $step_current = 4;
                        $step_next = 5;
                        if (!is_null($checkTarqib->isim_mmj)) {
                            $step_current = 5;
                            $step_next = 5;
                        }
                    }
                }
            }
            if ($checkTarqib->jenis == 'isim') {
                if (!is_null($checkTarqib->isim_mr)) {
                    $dataBreadcumTarqib[] = $checkTarqib->isim_mr;
                    if (!is_null($checkTarqib->isim_mm)) {
                        $dataBreadcumTarqib[] = $checkTarqib->isim_mm;
                        if (!is_null($checkTarqib->isim_mmj)) {
                            $dataBreadcumTarqib[] = $checkTarqib->isim_mmj;
                        }
                    }
                }
            }
            if ($checkTarqib->jenis == 'isim') {
                if (is_null($checkTarqib->isim_mr)) {
                    $dataOpt = $this->enumJenisIsim();
                    $columnStore = 'isim_mr';
                }

                if (!is_null($checkTarqib->isim_mr) && is_null($checkTarqib->isim_mm)) {
                    $dataOpt = $this->enumJenisIsimMM();
                    $columnStore = 'isim_mm';
                }

                if (!is_null($checkTarqib->isim_mr) && !is_null($checkTarqib->isim_mm) && is_null($checkTarqib->isim_mmj)) {
                    $dataOpt = $this->enumJenisIsimMMJ();
                    $columnStore = 'isim_mmj';
                }
            }

            if ($checkTarqib->jenis == 'huruf') {
                $step_current = 2;
                $step_next = 2;
            }
        }

        // dd($dataBreadcumTarqib, $dataOpt, $step_current, $step_end, $columnStore);

        // $kata = PostKata::with(['vars' => function($sub) {
        //     $sub->with(['sugestAdmin', 'sugestUser']);
        // }, 'vars.tags'])->find($id);
        return response()->json([
            'status' => true,
            'data' => [
                'kata_id' => $id,
                'step_current' => $step_current,
                'step_end' => $step_end,
                'step_next' => $step_next,
                'column_store' => $columnStore,
                'tarqib_option' => $dataOpt,
                'tarqib_log' => $dataBreadcumTarqib
            ]
        ],200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $stepNext = $request->fieldTarqibStepNext;
        $stepEnd = $request->fieldTarqibStepEnd;

        if ($request->fieldColumnStore == 'jenis') {
            if ($request->fieldTarqibOption == 'fiil') {
                $stepNext = 2;
                $stepEnd = 3;
            }

            if ($request->fieldTarqibOption == 'isim') {
                $stepNext = 2;
                $stepEnd = 5;
            }
        }

        if ($request->fieldColumnStore == 'jenis_fiil') {
            if ($request->fieldTarqibOption == 'madi' || $request->fieldTarqibOption == 'mudlori') {
                $stepNext = 3;
                $stepEnd = 5;
            } else {
                $stepNext = 3;
                $stepEnd = 3;
            }
        }

        $arrUpdate = [
            $request->fieldColumnStore => $request->fieldTarqibOption,
            'progress' => ($stepNext / $stepEnd) * 100
        ];

        $checkPostKataTarqib = PostKataTarqib::where('kata_id', $id)->first();
        if ($checkPostKataTarqib) {
            // $save_tarqib = $checkPostKataTarqib->update($arrUpdate);
            $save_tarqib = PostKataTarqib::where('kata_id', $id)->update($arrUpdate);
        } else {
            $arrUpdate['kata_id'] = $id;
            $save_tarqib = PostKataTarqib::create($arrUpdate);
        }

        return response()->json([
            'status' => $save_tarqib,
            'message' => [
                'head' => $save_tarqib ? 'Berhasil' : 'Gagal',
                'body' => 'Manambahkan Tarqib'
            ]
        ], ($save_tarqib ? 200 : 500));
    }

    // public function updateVerified(Request $request) {
    //     $postKataVar = PostKataVar::find($request->id);
    //     $postKataVar->update([
    //         'verified_at' => date('Y-m-d H:i:s'),
    //         'verified_by' => session('user_id')
    //     ]);
    //     return response()->json([
    //         'status' => true,
    //         'message' => [
    //             'head' => 'Berhasil',
    //             'body' => 'Verfied nahwu'
    //         ]
    //     ], 200);
    // }

    private function enumJenis() {
        return [
            'Fiil' => 'fiil',
            'Isim' => 'isim',
            'Huruf' => 'huruf'
        ];
    }

    private function enumJenisFiil() {
        return [
            'Madi' => 'madi',
            'Mudlori' => 'mudlori',
            'Amar' => 'amar',
            'Nahi' => 'nahi'
        ];
    }

    private function enumJenisFiilMM() {
        return [
            'Mujarod' => 'mujarod',
            'Mazid' => 'mazid'
        ];
    }

    private function enumJenisFiilMM2() {
        return [
            'Ma`lum' => 'ma`lum',
            'Majhul' => 'majhul'
        ];
    }

    private function enumJenisIsim() {
        return [
            'Marifat' => 'marifat',
            'Nakiroh' => 'nakiroh'
        ];
    }

    private function enumJenisIsimMM() {
        return [
            'Mudzakar' => 'mudzakar',
            'Muannas' => 'muannas'
        ];
    }

    private function enumJenisIsimMMJ() {
        return [
            'Mufrod' => 'mufrod',
            'Muannas' => 'muannas',
            'Jamak' => 'jamak'
        ];
    }
}
