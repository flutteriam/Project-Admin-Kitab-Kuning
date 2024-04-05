<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'comments' => $this->comments,
            'content' => $this->content,
            'cover' => $this->cover,
            'created_at' => $this->created_at,
            'likes' => $this->likes,
            'description' => $this->description,
            'slugs' => $this->slugs,
            'status' => $this->status,
            'title' => $this->title,
            'type' => $this->type,
            'cate_name' => $this->category->name,
            'babs' => $this->babs,
            'haveLiked' => $this->haveLiked ?? false,
            'haveSaved' => $this->haveSaved ?? false,
        ];
    }
}
