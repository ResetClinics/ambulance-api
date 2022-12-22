import Ionicons from "react-native-vector-icons/Ionicons";
import React from "react";
import { createBottomTabNavigator } from "@react-navigation/bottom-tabs";
import { Brigade } from "../Brigade";
import { CurrentCall } from "../CurrentCall";

const Tab = createBottomTabNavigator();

export const Menu = () => {
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        tabBarIcon: ({ focused, color, size }) => {
          let iconName;

          if (route.name === 'Бригада') {
            iconName = focused
              ? 'ios-information-circle'
              : 'ios-information-circle-outline';
          } else if (route.name === 'Текущий вызов') {
            iconName = focused ? 'ios-list' : 'ios-list-outline';
          }

          // You can return any component that you like here!
          return <Ionicons name={iconName} size={size} color={color} />;
        },
        tabBarActiveTintColor: 'tomato',
        tabBarInactiveTintColor: 'gray',
      })}
    >
      <Tab.Screen name="Бригада" component={Brigade} options={{ tabBarBadge: 3 }} style={styles.root}/>
      <Tab.Screen name="Текущий вызов" component={CurrentCall} />
    </Tab.Navigator>
  );
}

const styles = {
  root: {
    color: 'red'
  }
}
