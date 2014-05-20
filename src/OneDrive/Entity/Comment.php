<?php
namespace OneDrive\Entity;

/**
 * Class Comment
 * @package OneDrive\Entity
 * @see http://msdn.microsoft.com/en-us/library/dn631832.aspx
 *
 * @property-read array $data - An array of Comment objects, if a collection of objects is returned.
 * @property-read string $id - The Comment object's id.
 * @property-read array $from - Info about the user who created the comment.
 * @property string $message - The text of the comment. The maximum length of a comment is 10,000 characters. Required.
 * @property-read string $created_time - The time, in ISO 8601 format, at which the comment was created.
 */
class Comment {

} 