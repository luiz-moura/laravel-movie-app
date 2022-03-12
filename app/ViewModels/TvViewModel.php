<?php

namespace App\ViewModels;

use Illuminate\Support\Carbon;
use Spatie\ViewModels\ViewModel;

class TvViewModel extends ViewModel
{
    public function __construct(
        public $popularTv,
        public $topRatedTv,
        public $genres,
    ) {
        //
    }

    public function popularTv()
    {
        return $this->formatTv($this->popularTv);
    }

    public function topRatedTv()
    {
        return $this->formatTv($this->topRatedTv);
    }

    public function genres()
    {
        return collect($this->genres)->mapWithKeys(function ($genre) {
            return [$genre['id'] => $genre['name']];
        });
    }

    private function formatTv($tv)
    {
        return collect($tv)->recursive()->map(function ($tvshow) {
            $genresFormatted = $tvshow['genre_ids']->mapWithKeys(function ($value) {
                return [$value => $this->genres()->get($value)];
            })->implode(', ');

            return $tvshow->merge([
                'poster_path' => "http://image.tmdb.org/t/p/w500{$tvshow['poster_path']}",
                'vote_average' => ($tvshow['vote_average'] * 10) . '%',
                'first_air_date' => Carbon::parse($tvshow['first_air_date'])->format('M d, Y'),
                'genres' => $genresFormatted,
            ])->only([
                'poster_path',
                'id',
                'genre_ids',
                'name',
                'vote_average',
                'overview',
                'first_air_date',
                'genres',
            ]);
        });
    }
}
