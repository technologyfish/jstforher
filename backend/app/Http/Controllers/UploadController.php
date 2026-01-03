<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * 上传图片
     */
    public function uploadImage(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|image|max:10240' // 最大10MB
        ]);

        $file = $request->file('file');
        
        // 生成文件名
        $extension = $file->getClientOriginalExtension();
        $filename = date('YmdHis') . '_' . Str::random(10) . '.' . $extension;
        
        // 按日期分目录
        $directory = 'uploads/images/' . date('Y-m');
        
        // 确保目录存在（使用绝对路径）
        $publicPath = base_path('public');
        $fullPath = $publicPath . '/' . $directory;
        
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
        
        // 移动文件
        $file->move($fullPath, $filename);
        
        // 返回相对路径（带完整URL）
        $baseUrl = env('APP_URL', 'http://localhost:8000');
        $url = $baseUrl . '/' . $directory . '/' . $filename;

        return response()->json([
            'success' => true,
            'data' => [
                'url' => $url,
                'filename' => $filename,
                'path' => $directory . '/' . $filename
            ]
        ]);
    }

    /**
     * 批量上传图片
     */
    public function uploadImages(Request $request)
    {
        $this->validate($request, [
            'files' => 'required|array',
            'files.*' => 'image|max:10240'
        ]);

        $uploadedFiles = [];
        $publicPath = base_path('public');
        $baseUrl = env('APP_URL', 'http://localhost:8000');
        
        foreach ($request->file('files') as $file) {
            // 生成文件名
            $extension = $file->getClientOriginalExtension();
            $filename = date('YmdHis') . '_' . Str::random(10) . '.' . $extension;
            
            // 按日期分目录
            $directory = 'uploads/images/' . date('Y-m');
            
            // 确保目录存在
            $fullPath = $publicPath . '/' . $directory;
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
            
            // 移动文件
            $file->move($fullPath, $filename);
            
            // 返回完整 URL
            $url = $baseUrl . '/' . $directory . '/' . $filename;
            
            $uploadedFiles[] = [
                'url' => $url,
                'filename' => $filename,
                'path' => $directory . '/' . $filename
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $uploadedFiles
        ]);
    }
}

