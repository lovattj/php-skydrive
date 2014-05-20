<?php
namespace OneDrive\Entity;

/**
 * Class Album
 * @package OneDrive\Entity
 * @see http://msdn.microsoft.com/en-us/library/dn631830.aspx
 *
 * @property-read array $data - An array container for Album objects when a collection of objects is returned.
 * @property-read string $id - The Album object's ID.
 * @property-read array $from - Info about the user who authored the album.
 * @property string $name - The name of the album. This structure is required when creating the object.
 * @property string|null $description - A description of the album, or null if no description is specified.
 * @property-read string $parent_id - The resource ID of the parent.
 * @property-read string $upload_location - The URL to upload items to the album, hosted in OneDrive. Requires the wl.skydrive scope.
 * @property-read bool $is_embeddable - A  value that  indicates whether this album can be embedded. If this album can be embedded, this value is true; otherwise, it is false.
 * @property-read int $count - The total number of items in the album.
 * @property-read string $link - A URL of the album, hosted in OneDrive.
 * @property-read string $type - The type of object; in this case, "album".
 * @property-read array $shared_with - The object that contains permissions info for the album. Requires the wl.skydrive scope.
 * @property-read string $created_time - The time, in ISO 8601 format, at which the album was created.
 * @property-read string $updated_time - The time, in ISO 8601 format, that the system updated the  album last.
 * @property-read string $client_updated_time - The time, in ISO 8601 format, that the file was last updated.

 */
class Album extends Entity{

} 