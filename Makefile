skeletons = nette wp
repo_path = ssh://dc/var/cache/git


all:
	@echo "Select particular task, no rule to make all."

test:
	phpunit --no-globals-backup tests

$(skeletons):
	git remote add $@-skeleton $(repo_path)/$@-skeleton.git
	git fetch $@-skeleton
	git rebase master $@-skeleton/master
	git checkout -b $@-skeleton
	git checkout master
	git merge $@-skeleton
	git branch -d $@-skeleton
	git remote rm $@-skeleton

hooks: local_config.ini
	git remote add deployment_scripts $(repo_path)/deployment_scripts.git
	git fetch deployment_scripts
	git stash
	git checkout deployment_scripts/master
	cp --verbose --backup=numbered bin/pre-commit .git/hooks/
	cp --verbose --backup=numbered bin/get_alter_export.pl .git/hooks/
	cp --verbose --backup=numbered bin/apply_alter_export.pl .git/hooks/
	cp --verbose --backup=numbered bin/post-merge .git/hooks
	git checkout master
	git stash apply
	git remote rm deployment_scripts

