<?php
namespace OneDrive\Entity;

/**
 * Class Photo
 * @package OneDrive\Entity
 * @see http://msdn.microsoft.com/en-us/library/dn631841.aspx
 * @property-read array $data - An array of Photo objects, when a collection of objects is returned.
 * @property-read string $id - The Photo object's ID.
 * @property-read array $from - Info about the user who uploaded the photo.
 * @property string $name - The file name of the photo. Required.
 * @property string|null $description - A description of the photo, or null if no description is specified.
 * @property-read string $parent_id - The ID of the folder where the item is stored.
 * @property-read int $size - The size, in bytes, of the photo.
 * @property-read int $comments_count - The number of comments associated with the photo.
 * @property-read bool $comments_enabled - A value that indicates whether comments  are enabled for the photo. If  comments can be made, this value is true; otherwise, it is false.
 * @property-read int $tags_count - The number of tags on the photo.
 * @property-read bool $tags_enabled - A value that indicates whether tags are enabled for the photo. If  users can tag the photo, this value is true; otherwise, it is false.
 * @property-read bool $is_embeddable - A  value that  indicates whether this photo can be embedded. If this photo can be embedded, this value is true; otherwise, it is false.
 * @property-read string $picture - A URL of the photo's picture.
 * @property-read string $source - The download URL for the photo.
 * @property-read string $upload_location - The URL to upload photo content hosted in OneDrive. This value is returned only if the wl.skydrive scope is present.
 * @property-read array $images - Info about various sizes of the photo.
 * @property-read string $link - A URL of the photo, hosted in OneDrive.
 * @property-read string|null $when_taken - The date, in ISO 8601 format, on which the photo was taken, or null if no date is specified.
 * @property-read int $height - The height, in pixels, of the photo.
 * @property-read int $width - The width, in pixels, of the photo.
 * @property-read string $type - The type of object; in this case, "photo".
 * @property-read array $location - The location where the photo was taken.
 * @property-read string $camera_make - The manufacturer of the camera that took the photo.
 * @property-read string $camera_model - The brand and model number of the camera that took the photo.
 * @property-read double $focal_ratio - The f-number that the photo was taken at.
 * @property-read double $focal_length - The focal length that the photo was taken at, typically expressed in millimeters for newer lenses.
 * @property-read double $exposure_numerator - The numerator of the shutter speed (for example, the "1" in "1/15 s") that the photo was taken at.
 * @property-read double $exposure_denominator - The denominator of the shutter speed (for example, the "15" in "1/15 s") that the photo was taken at.
 * @property-read array $shared_with - The object that contains permissions info for the photo.
 * @property-read string $created_time - The time, in ISO 8601 format, at which the photo was created.
 * @property-read string $updated_time - The time, in ISO 8601 format, at which the photo was last updated.
 */
class Photo {

} 