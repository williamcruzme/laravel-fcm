# Contributing to Laravel FCM
First of all, thanks for taking interest into contributing to this repository, below is what you need to know about the project.

### Getting Started

Fork the repository, or clone it:

```bash
git clone https://github.com/williamcruzme/laravel-fcm
```

Install dependencies using [Composer](https://getcomposer.org/download/):

```bash
composer install
```

### Folder Structure

As you can see we have:

- `database`: contains the migrations, seeders or factories.
- `src` contains the working code for the repository:
  - `Exceptions`: contains the exceptions.
  - `Facades`: contains the package facades.
  - `Http`: contains the http utilities like controllers, requests, etc.

### Issues

When creating issues, please provide as much details as possible. A clear explanation on the issue and a reliable production example can help us greatly in improving this project. Your issue may get closed if it cannot be easily reproduced.

If your issue gets closed for not providing enough info or not responding to the maintainers' comments, do not consider it a hostile action. There are probably other issues that the maintainers are working on and must give priority to issues that are well investigated, you can always revisit the issue and address the reasons that it was closed and we will be happy to re-open it and address it properly. Sometimes a commit will close your issue without a response from the maintainers so make sure you read the issue timeline to prevent any misunderstandings.

### Code Style

The code style is enforced with [Laravel Coding Style](https://laravel.com/docs/master/contributions#coding-style). Any violation of the code style may prevent merging your contribution so make sure you follow it. And yes we love the perfection.

### Pull Requests

- Make sure you fill the PR template provided.
- PRs should have titles that are clear as possible.
- Make sure that your PR is up to date with the branch you are targeting, use `git rebase` for this.
- Unfinished/In-Progress PRs should have `[WIP]` prefix to them, and preferably a checklist for ongoing todos.
- Make sure to mention which issues are being fixed by the PR so they can be closed properly.
- Make sure to preview all pending PRs to make sure your work won't conflict with other ongoing pull-request.
- Coordinate with ongoing conflicting PRs' authors to make it easier to merge both your PRs.

### Source Code

Currently we are using PHP 7 and Laravel 6.x for the source code.
