remote:
  path: /var/www
  releaseDir: releases
  symlink: current
  keepReleases: 5

code:
  scm: git
  repo: git@github.com:MyOrg/repo.git
  branch: master
  username: ~
  password: ~

authentication:
  username: user
  password: ~
  key: /path/to/key.pub
  passphrase: ~

environments:

  production:
    preDeploy:
    strategy: release, link, cleanup
    postDeploy:
    servers:
      - host: server-001.prod.example.com
        strategy: release, migrate, link, cleanup
      - host: server-002.prod.example.com
      - host: server-003.prod.example.com

  staging:
    preDeploy:
    strategy: release, link, cleanup
    postDeploy:
    servers:
      - host: staging.server.com
        strategy: release, link, cleanup