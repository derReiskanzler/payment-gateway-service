load('ext://helm_resource', 'helm_resource', 'helm_repo')
load('ext://uibutton', 'cmd_button', 'text_input', 'location')
load('ext://syncback', 'syncback')

### Start config
global_tiltfile = '{}/{}'.format(str(local("brew --prefix lokal", quiet = True)).rstrip("\n"), "Tiltfile")

functions = load_dynamic(global_tiltfile)
get_config = functions['get_config']
get_value = functions['get_value']
get_arch = functions['get_arch']
service_setup = functions['service_setup']
pubsub_emulator_ui = functions['pubsub_emulator_ui']

config = get_config('tilt_config.yaml')
custom_config = get_config('.tilt_custom.yaml')
microservices_infrastructure_path = get_value(custom_config, 'global.microservices_infrastructure_path')
arch = get_arch()

watch_file('tilt_config.yaml')
watch_file('.tilt_custom.yaml')
### End config

for sv in config.get('services'):
  service = {
    "name": sv.get('name'),
    "namespace": sv.get('namespace'),
    "labels": sv.get('labels'),
    "deps": sv.get('dependencies'),
    "live_reload": sv.get('live_reload'),
    "flags": sv.get('flags'),
    "buttons": sv.get('buttons'),
    "set_value_from_file": sv.get('set_value_from_file'),
    "arm64_image": sv.get('arm64_image'),
    "syncback": sv.get('syncback'),
    "xdebug": sv.get('xdebug'),
    "sync": sv.get('sync'),
    "docker_build": sv.get('docker_build'),
  }

  service_setup(service, microservices_infrastructure_path, arch)

pubsub_emulator_ui()
