<?php
namespace OneDrive\Entity;

/**
 * Class File
 * @package OneDrive\Entity
 * @see http://msdn.microsoft.com/en-us/library/dn631834.aspx
 *
 * @property-read array $data - An array of File objects, if a collection of objects is returned.
 * @property-read string $id - The File object's ID.
 * @property-read array from - Info about the user who uploaded the file.
 * @property string name - The name of the file. Required.
 * @property string|null description - A description of the file, or null if no description is specified.
 * @property-read string parent_id - The ID of the folder the file is currently stored in.
 * @property-read int size - The size, in bytes, of the file.
 * @property-read string upload_location - The URL to upload file content hosted in OneDrive.
 * @property-read int comments_count - The int of comments that are associated with the file.
 * @property-read true/false comments_enabled - A value that indicates whether comments are enabled for the file. If comments can be made, this value is true; otherwise, it is false.
 * @property-read true/false is_embeddable - A  value that  indicates whether this file can be embedded. If this file can be embedded, this value is true; otherwise, it is false.
 * @property-read string source - The URL to use to download the file from OneDrive.
 * @property-read string link - A URL to view the item on OneDrive.
 * @property-read string type - The type of object;  in this case, "file".
 * @property-read object shared_with - Object that contains permission info.
 * @property-read string created_time - The time, in ISO 8601 format, at which the file was created.
 * @property-read string updated_time - The time, in ISO 8601 format, that the system updated the file last.
 * @property-read string client_updated_time - The time, in ISO 8601 format, that the client machine updated the file last.
 * @property string sort_by - Sorts the items to specify the following criteria: updated, name, size, or default.
 */
class File extends Entity{

} 