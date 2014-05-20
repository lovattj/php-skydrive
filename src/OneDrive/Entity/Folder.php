<?php
namespace OneDrive\Entity;

/**
 * Class Folder
 * @package OneDrive\Entity
 * @see http://msdn.microsoft.com/en-us/library/dn631836.aspx
 *
 * @property-read array $data - An array container for Folder objects, if a collection of objects is returned.
 * @property-read string $id - The Folder object's ID.
 * @property-read array $from - Info about the user who created the folder.
 * @property string $name - The name of the folder. Required.
 * @property string|null $description - A description of the folder, or null if no description is specified.
 * @property-read int $count - The total number of items in the folder.
 * @property-read string $link - The  URL of the folder, hosted in OneDrive.
 * @property-read string $parent_id - The resource ID of the parent.
 * @property-read string $upload_location - The URL to upload items to the folder hosted in OneDrive. Requires the wl.skydrive scope.
 * @property-read bool $is_embeddable - A  value that  indicates whether this folder can be embedded. If this folder can be embedded, this value is true; otherwise, it is false.
 * @property-read string $type - The type of object; in this case, "folder".
 * @property-read string $created_time - The time, in ISO 8601 format, at which the folder was created.
 * @property-read string $updated_time - The time, in ISO 8601 format, that the system updated the file last.
 * @property-read string $client_updated_time - The time, in ISO 8601 format, that the client machine updated the file last.
 * @property-read array $shared_with - Permissions info for the folder. Requires the wl.skydrive scope.
 * @property string $sort_by - Sorts the items to specify the following criteria: updated, name, size, or default.
 */
class Folder extends Entity{

} 