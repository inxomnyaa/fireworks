![plugin icon](https://github.com/thebigsmileXD/fireworks/blob/master/resources/fireworks_icon.png)

# fireworks [![](https://poggit.pmmp.io/shield.state/fireworks)](https://poggit.pmmp.io/p/fireworks)
Adds Fireworks to Pocketmine

Its simply adding firework items and entities into PocketMine-MP servers.

You can launch any firework from the creative inventory AND custom ones - just like in vanilla MCPE.

# How to get a firework
Either, give a firework via gamemode 1, or /give command.
For generating the /give command i suggest using: https://mcstacker.bimbimma.com/mcstacker1.10.php and modifying the command to work with PocketMine-MP. This is not easy. No need to explain further.

The easier way is to use the code to create custom fireworks. See #Create-a-custom-firework for that.

# Create a custom firework
This is sample code to explain the "API"-alike part of the plugin, giving 2 custom fireworks:

Without special explosions:
```
		$data = new FireworksData();
		$data->flight = 2;//flight time. default is 1
		$firework = new Fireworks();
		$nbt = Fireworks::ToNbt($data);//create the compound tag
		$firework->setNamedTag($nbt);//modify the item's compound tag
		$event->getPlayer()->getInventory()->addItem($firework);//give the item to a player (this case: PlayerJoinEvent $event)
```

With special explosions
```
		$explosion = new FireworksExplosion();//init an explosion
		$explosion->fireworkColor = [4, 4, 4];//set the color to custom colors
		$explosion->fireworkFade = [5, 5, 5];//set the fade color to custom colors
		$explosion->fireworkFlicker = true;//enable the flickering
		$explosion->fireworkTrail = false;//enable the trail
		$explosion->fireworkType = 4;//set the shape/type of the firework
		$data = new FireworksData();//create the generic data
		$data->flight = 2;
		$data->explosions[] = $explosion;//add the explosion
		$firework = new Fireworks();
		$nbt = Fireworks::ToNbt($data);
		$firework->setNamedTag($nbt);
		$event->getPlayer()->getInventory()->addItem($firework);
```