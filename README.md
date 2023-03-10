# My Status

Simple script to store and update a personal status. The current status can be publicly accessed and referenced
elsewhere.

The status can only be updated with a valid secret key. The current status is stored in a simple json file.

## Setup

Make sure the config file contains a good key, that the key is usable in a GET request and that the specified storage
files are writable.

The storage files are created if they do not exist. Since the activity file does not contain any sensitive information,
it
can be made publicly accessible. At least it contains nothing that is not already available over the status itself.

The templates file can also be publicly accessible. When it is first created, default templates are added, which are
specified in `Templates.loadDefaultTemplates()`. The templates can be modified or new ones can be added. The templates
are stored as json objects. It is recommended to let it create the templates file on the first request, and then go in
and modify it to your liking.

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

| Parameter          | Description                                                                                                                                             | Example                                             |
|--------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------|-----------------------------------------------------|
| `title`            | The title of the status.                                                                                                                                | `title=Working`                                     |
| `description`      | A description of the status.                                                                                                                            | `description=Making progress on personal projects.` |
| `expectedDuration` | The expected duration of the status in seconds.                                                                                                         | `duration=3600`                                     |
| `available`        | Whether you are available to other people.                                                                                                              | `available=0`                                       |
| `working`          | Whether you are working, as opposed to having some personal time.                                                                                       | `working=1`                                         |
| `template`         | If a valid template id is specified, the template is applied to the activity. If other parameters are set, they will overwrite the template parameters. | `template=work`                                     |

## Templates

Templates are a way to easily set often used status. They can either be specified as `template` parameter for specific
activity, or using simple rules and automations.

A template overwrites all parameters of an activity, only the `startTime` and `template` parameters are preserved. If
the template is applied via the `template` paramter, other specified parameters will overwrite the template parameters.

### Rules

Rules are checked every time the status is requested. If all rules of a template match, the template is applied. Each
template has a priority. The valid template with the highest priority is applied.

| Rule                      | Description                                                                       | Further parameters                                   |
|---------------------------|-----------------------------------------------------------------------------------|------------------------------------------------------|
| `triggerOnlyOnEmptyTitle` | Only apply the template if the title is empty.                                    |                                                      |
| `triggerAfterTimeout`     | Applies the template if the current activity is older than the specified timeout. | `timeout` specifies the required timeout in seconds. |
