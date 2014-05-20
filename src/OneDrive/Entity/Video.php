<?php
namespace OneDrive\Entity;

/**
 * Class Video
 * @package OneDrive\Entity
 *
 * @property-read array $data - An array of Video objects, if a collection of objects is returned.
 * @property-read string $id - The Video object's ID.
 * @property-read array $from - Info about the user who uploaded the video.
 * @property string $name - The file name of the video. Required.
 * @property string|null $description - A description of the video, or null if no description is specified.
 * @property-read string $parent_id - The id of the folder where the item is stored.
 * @property-read int $size - The size, in bytes, of the video.
 * @property-read int $comments_count - The number of comments that are associated with the video.
 * @property-read bool $comments_enabled - A value that indicates whether comments are enabled for the video. If  comments can be made, this value is true; otherwise, it is false.
 * @property-read int $tags_count - The number of tags on the video.
 * @property-read bool $tags_enabled - A value that indicates whether tags are enabled for the video. If  tags can be set, this value is true; otherwise, it is false.
 * @property-read bool $is_embeddable - A value that indicates whether this video can be embedded. If this video can be embedded, this value is true; otherwise, it is false.
 * @property-read string $picture - A URL of a picture that represents the video.
 * @property-read string $source - The download URL for the video.
 * @property-read string $upload_location - The URL to upload video content, hosted in OneDrive. This value is  returned only if the wl.skydrive scope is present.
 * @property-read string $link - A URL of the video, hosted in OneDrive.
 * @property-read int $height - The height, in pixels, of the video.
 * @property-read int $width - The width, in pixels, of the video.
 * @property-read int $duration - The duration, in milliseconds, of the video run time.
 * @property-read int $bitrate - The bit rate, in bits per second, of the video.
 * @property-read string $type - The type of object; in this case, "video".
 * @property-read array $shared_with - The object that contains permission  info.
 * @property-read string $created_time - The time, in ISO 8601 format, at which the video was created.
 * @property-read string $updated_time - The time, in ISO 8601 format, at which the video was last updated.
 */
class Video {

} 