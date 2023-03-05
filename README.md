# My Status

Simple script to store and update a personal status. The current status can be publicly accessed and referenced
elsewhere.

The status can only be updated with a valid secret key. The current status is stored in a simple json file.

## Setup

Make sure the config file contains a good key, that the key is usable in a GET request and that the specified storage
file is writable.

The storage file is created if it does not exist. Since the storage file does not contain any sensitive information, it
can be made publicly accessible. At least it contains nothing that is not already available over the status itself.

## Usage

### Get status

Simple GET request to the `index.php`.

The status is returned as a json object. Some information is dynamically updated. Durations are usually given in seconds
and timestamps as UNIX timestamps in seconds.

### Update status

For simplicity, this also consists of a simple GET request. The secret key must be passed as a GET parameter and is the
only required parameter. All additional parameters are optional. They can and should be used to specify the status to
your liking.

Here is an overview:

| Parameter     | Description                                                       | Example                                             |
|---------------|-------------------------------------------------------------------|-----------------------------------------------------|
| `title`       | The title of the status.                                          | `title=Working`                                     |
| `description` | A description of the status.                                      | `description=Making progress on personal projects.` |
| `duration`    | The expected duration of the status in seconds.                   | `duration=3600`                                     |
| `available`   | Whether you are available to other people.                        | `available=0`                                       |
| `working`     | Whether you are working, as opposed to having some personal time. | `working=1`                                         |


