<?php
namespace OneDrive\Entity;

/**
 * Class Audio
 * @package OneDrive\Entity
 * @see http://msdn.microsoft.com/en-us/library/dn631831.aspx
 *
 * @property-read array $data - An array of Audio objects, if a collection of objects is returned.
 * @property-read string $id - The Audio object's ID.
 * @property-read array $from - Info about the user who uploaded the audio.
 * @property string $name - The name of the audio. Required.
 * @property string|null $description - A description of the audio, or null if no description is specified.
 * @property-read string $parent_id - The id of the folder in which the audio is currently stored.
 * @property-read int $size - The size, in bytes, of the audio.
 * @property-read string $upload_location - The URL to use to upload a new audio to overwrite the existing audio.
 * @property-read int $comments_count - The number of comments associated with the audio.
 * @property-read bool $comments_enabled - A value that indicates whether comments are enabled for the audio. If  comments can be made, this value is true; otherwise, it is false.
 * @property-read bool $is_embeddable - A value that indicates whether this audio can be embedded. If this audio can be embedded, this value is true; otherwise, it is false.
 * @property-read string $source - The URL to use to download the audio from OneDrive.
 * @property-read string $link - A URL to view the item on OneDrive.
 * @property-read string $type - The type of object;  in this case, "audio".
 * @property string $title - The audio's title.
 * @property string $artist - The audio's artist name.
 * @property string $album - The audio's album name.
 * @property string $album_artist - The artist name of the audio's album.
 * @property string $genre - The audio's genre.
 * @property-read integer $duration - The audio's playing time, in milliseconds.
 * @property-read string $picture - A URL to view the audio's picture on OneDrive.
 * @property-read array $shared_with - The object that contains permission info.
 * @property-read string $access (shared_with object) - Info about who can access the audio. The options  are:
 * @property-read string $created_time - The time, in ISO 8601 format, at which the audio was created.
 * @property-read string $updated_time - The time, in ISO 8601 format, at which the audio was last updated.

 */
class Audio extends Entity{

} 