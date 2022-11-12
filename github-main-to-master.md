# github `main` branch : A pain in the ass !

I'm not realy skill with `git`, and this is worst a nightmare with that `main` branch imposed by [github](https://github.com) since a certain time, that I use not that much.
So I write this note as a reminder for next times.

- Working with `main` branch, [No `main` on local, but on remote](#10---no-main-on-local-but-on-remote)
- I only want `master` branch, [I do not want that `main` in my project](#20---i-do-not-want-that-main-in-my-project)

---

## 1.0 - No `main` on local, but on remote

Several scenarii there. You want the `main` branch in your project, but it doesn't exists yet.

### 1.1 - I want that branch `main` in my project
First, we need to change the branch, with the [`checkout` command](#git-helps):
```shell
$ git checkout main
```

If you got a message `error: pathspec 'main' did not match any file(s) known to git` , then you have to create the `main` branch. Like so:
```shell
$ git checkout -b main [--track origin/main]
```

When the `checkout` goes well, we have to merge `master` into `main`:
```shell
$ git merge master
```

Then we can push:
```shell
$ git push origin main
```
> #### What if you want to remove that `master` and only use `main` in your locale git ?
>
> **!!! caution !!! Your project should be merged in the `main` branch**
>
> ```shell
> $ git branch --delete --force master
> ```
>
> Then always use:
> `$ git push -u origin main`


### 1.2 - I pushed to `master` and need to merge to `main` on github

> #### Merging branch `master` into `main`
>
> - Go to `Pull Request` tab
> - Click on the `Pull request` you have created
> - Scroll down to the `Merge Pull Request` Button
> - Click that button

---

### 2.0 - I do not want that `main` in my project

This is the scenario where you pushed to `master` and the `main` branch is boring you, because your push is not appearing.

> #### Switching the github default branch `main` to `master`
>
> - Click the `Settings` tab
> - Click the `Branches` menu on the left
> _From here, we'll change the default branch to `master`_
> - Under _Default branch_, right to `main`, there's an icon to edit the default branch, click and choose `master`
> _From here, your project is now appearing_
> - Go back to your github project
> - Click on `branch` (indicating the number of branches), right to `master`
> - Delete the `main` branch

---

## Git helps

```shell
$ git help checkout
$ git help branch
```

---

## History
- 2022/07/16 : First lines.

---

[Guitarneck - July 2022](https://github.com/guitarneck)