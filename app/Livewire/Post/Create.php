<?php

namespace App\Livewire\Post;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use LivewireUI\Modal\ModalComponent;

class Create extends ModalComponent
{


    use WithFileUploads;

    public $media = [];
    public $description;
    public $location;

    public $hide_like_view = false;

    public $allow_commenting = false;






    function submit()
    {





        #validate
        $this->validate([

            'media.*' => 'required|file|mimes:png,jpg,mp4,jpeg|max:50288',
            'allow_commenting' => 'boolean',
            'hide_like_view' => 'boolean',

        ]);



        #Determine if Reel or Post
        $type = $this->getPostType($this->media);


        #create post
        $post =  Post::create(
            [
                'user_id' => auth()->user()->id,
                'description' => $this->description,
                'location' => $this->location,
                'allow_commenting' => $this->allow_commenting,
                'hide_like_view' => $this->hide_like_view,
                'type' => $type
            ]
        );



        #add Media
        foreach ($this->media as $key => $media) {
            #first get mime type by creating helper function
            $mime = $this->getMime($media);

            #make sure disk is public
            #!!!!! run command php artisan storage:link to access the file from /storage/media/...
            $path = $media->store('media', 'public');

            $url = url(Storage::url($path));


            #create media
            Media::create([
                'url' => $url,
                'mime' => $mime,
                'mediable_id' => $post->id,
                'mediable_type' => Post::class
            ]);
        }


        $this->reset();
        $this->dispatch('close');




        #dispatch to listen livewire component Home
        #reference livewire docs
        $this->dispatch('post-created', $post->id);


        #add this
        #In next video Get banner from pines-UI and add it to the layout
        return   $this->dispatch('created');
    }


    private function getMime($media): string
    {

        if (str()->contains($media->getMimeType(), 'video')) {

            return 'video';
        } else if (str()->contains($media->getMimeType(), 'image')) {
            # code...
            return 'image';
        }
    }


    private function getPostType($media): string
    {

        if (count($this->media) === 1 && str()->contains($this->media[0]->getMimeType(), 'video')) {
            return 'reel';
        } else {

            return 'post';
        }
    }





    /**
     * Supported: 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'
     */
    public static function modalMaxWidth(): string
    {
        return '4xl';
    }

    public function render()
    {
        return view('livewire.post.create');
    }
}
