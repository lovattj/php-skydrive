<?php
namespace OneDrive;

class StatusCodes {
    /**
     * The request was successful.
     */
    const OK = 200;

    /**
     * The resource was created.
     */
    const CREATED = 201;

    /**
     * The resource was deleted.
     */
    const DELETED = 204;

    /**
     * The response is a redirect to a specific location.
     */
    const REDIRECT = 302;

    /**
     * Bad request. See the response error message for more details.
     */
    const BAD_REQUEST = 400;

    /**
     * The token was invalid, missing, or expired.
     */
    const REQUEST_TOKEN_EXPIRED = 401;

    /**
     * The token was not authorized, or the user has not been granted permissions.
     */
    const NOT_AUTHORIZED_TOKEN = 403;

    /**
     * The resource was not found.
     */
    const NOT_FOUND = 404;

    /**
     * An invalid method was used.
     */
    const INVALID_METHOD = 405;

    /**
     * The request timed out.
     */
    const TIMEOUT = 408;

    /**
     * The request failed, due to an edit conflict.
     */
    const FAILED = 409;

    /**
     * The endpoint or scenario is no longer supported.
     */
    const NOT_SUPPORTED = 410;

    /**
     * The request entity body was too large.
     */
    const LARGE_BODY = 413;

    /**
     * The request entity body was an invalid media type.
     */
    const INVALID_MIME = 415;

    /**
     * The request was throttled. Throttling occurs on a per/app and per/user basis.
     */
    const THROTTLED = 420;

    /**
     * The requested resource is locked and can't be updated.
     */
    const LOCKED = 423;

    /**
     * There is a connection issue with the client.
     */
    const CONNECTION_BROBLEM = 499;

    /**
     * The server had an unexpected error.
     */
    const ERROR = 500;

    /**
     * The server is unavailable.
     */
    const UNAVAILABLE = 503;

    /**
     * The user doesn't have enough available storage.
     */
    const NOT_ENOUGH_SPACE = 507;
} 