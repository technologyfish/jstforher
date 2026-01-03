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

        try {
            $file = $request->file('file');
            
            // 生成文件名
            $extension = $file->getClientOriginalExtension();
            $filename = date('YmdHis') . '_' . Str::random(10) . '.' . $extension;
            
            // 按日期分目录
            $monthDir = date('Y-m');
            $directory = 'uploads/images/' . $monthDir;
            
            // 使用绝对路径（适配 SiteGround）
            $publicPath = base_path('public');
            $fullPath = $publicPath . '/' . $directory;
            
            // 确保目录存在
            if (!file_exists($fullPath)) {
                // 使用 0775 权限以确保在共享主机上有足够的权限
                if (!mkdir($fullPath, 0775, true)) {
                    throw new \Exception("Failed to create directory: {$fullPath}");
                }
                // 尝试更改目录权限（如果 mkdir 的权限不够）
                @chmod($fullPath, 0775);
            }
            
            // 验证目录可写
            if (!is_writable($fullPath)) {
                throw new \Exception("Directory is not writable: {$fullPath}");
            }
            
            // 移动文件
            if (!$file->move($fullPath, $filename)) {
                throw new \Exception("Failed to move uploaded file");
            }
            
            // 验证文件已上传
            $uploadedFile = $fullPath . '/' . $filename;
            if (!file_exists($uploadedFile)) {
                throw new \Exception("File upload verification failed");
            }
            
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
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
                mkdir($fullPath, 0775, true);
                @chmod($fullPath, 0775);
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

