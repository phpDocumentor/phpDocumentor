README
======

## Publication / Deployment

When this documentation is updated

1. Docker Hub will detect the change
2. Docker Hub builds a new image
3. Docker Hub pushes it to its registry
4. Keel.sh on our cluster will detect the new image
5. Keel.sh will auto-update the deployment on our cluster

This means that Deployment is fully automated; any change
will be pushed live as soon as it hits a branch.
