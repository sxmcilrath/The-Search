<?php


/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     * This should get all the albums for one particular user. 
     * Should be guarded by sanctum 
     */
    public function index(Request $request)
    {
        //can use eloquent to fetch the albums off the rip
        $albums = $request->user()
            ->albums()
            ->with(['artists', 'tracks']) //preloading the artists and tracks to avoid unessary queries
            ->get();

        return response()->json($albums);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *  only storing when a user saves it to their library in some way or fashion. 
     * 
     *  also need to check if artist exists, call artist.store if not, this should happen from 
     *  FE, it needs spotify links 
     * 
     * User is providing the status:
     *      there will be some dropdown to default add to library as to-listen
     *      or other options
     * User info automatically provided
        [Incoming POST Payload]
        │
        ├──► Start DB Transaction
        │      │
        │      ├──► 1. Find or Create Artists (firstOrCreate)
        │      ├──► 2. Find or Create Album (firstOrCreate)
        │      ├──► 3. Attach Album to User (with status/score)
        │      ├──► 4. Bulk insert Tracks (associated with album_id)
        │      └──► 5. Associate Tracks with Artists (pivot)
        │
        ├──► Commit Transaction
        │
        └──► Return 201 Created Response
     */

    public function store(Request $request)
    {
        //need to validate artist, album, user, tracks info 
        //don't need to val user (protected by sanctum)
        $validated = $request->validate([
            'artists'            => 'required|array',
            'artists.*.spotify_id' => 'required|string|max:100', //may not be in table yet
            'artists.*.name'       => 'required|string|max:1000',
            'album'             => 'required|array',
            'album.spotify_id'  => 'required|string|max:100', //TODO figure out where prevention logic of dupe albums
            'album.name'        => 'required|string|max:1000',
            'album.release_date'=> 'required|string|max:20', //sent as "1981-12"
            'album.status'      => 'required|in:to-listen,listening,listened', 
            'album.image_url'   => 'sometimes|required|string|max:100',
            'tracks'            => 'required|array',
            'tracks.*.spotify_id' => 'required|string|max:100',
            'tracks.*.name'       => 'required|string|max:1000',
            'tracks.*.number'     => 'required|integer|max:2000',
            'tracks.*.seconds'    => 'required|integer',
            //should be fine w/ no album id, establish in logic below 
            
        ]);

        $ret = DB::transaction(function () use ($validated, $request){
            
            //retrieve all artists (create if new artist)
            $artists = [];
        
            foreach($validated['artists'] as $valArtist) {
                $artists[] = Artist::firstOrCreate(
                    ['spotify_id' => $valArtist['spotify_id']],
                    [
                        'spotify_id' => $valArtist['spotify_id'],
                        'name'       => $valArtist['name']
                    ]
                );
            }
    
            //add or retrieve album (should always be adding here)
            //cant help but think there should be some indicaiton if the item has alr been created
            
            //handle edge case fields 
            $imageURL = $validated['album']['image_url'] ?? null; //sometimes case
            $date = Carbon::parse($validated['album']['release_date']);
    
            $album = Album::firstOrCreate(
                ['spotify_id' => $validated['album']['spotify_id']],
                [
                    'spotify_id' => $validated['album']['spotify_id'],
                    'name' => $validated['album']['name'],
                    'status' => $validated['album']['status'],
                    'release_date' => $date,
                    'image_url' => $imageURL,
                    'user_id' => $request->user()->id //why is it user() and not just user?
                ]
            );
    
            //skip a lot of the following code if this was an existing album
            if (!$album->wasRecentlyCreated){
                $album->load(['tracks', 'artists']);
                return response()->json($album, 200); //should we pull tracks? 
            }
    
            //m2m association
            $album->artists()->attach(collect($artists)->pluck('id')->all());
            
            //create tracks if needed
            $tracks = [];
            foreach($validated['tracks'] as $valTrack){
    
                //going to be slightly ineffiecient but we need 
                //the association
                $track = 
                    [
                        'album_id' => $album->id,
                        'spotify_id' => $valTrack['spotify_id'],
                        'name' => $valTrack['name'],
                        'number' => $valTrack['number'],
                        'seconds' =>$valTrack['seconds']
                    ];
    
                $track = Track::create($track);
                $track->artists()->attach(collect($artists)->pluck('id')->all()); //m2m assoc
                $tracks[] = $track;
            }
    
            //load and send in json
            $album->load(['tracks', 'artists']);
            return response()->json($album, 201);

        });

        return $ret;
    }

    /**
     * Display the specified resource.
     */
    public function show(Album $album)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Album $album)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Album $album)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Album $album)
    {
        //
    }
}
