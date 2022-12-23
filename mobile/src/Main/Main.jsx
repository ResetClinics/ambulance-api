import React from "react";
import { NavigationContainer } from "@react-navigation/native";
import { Routes } from "../navigation/Routes";

export const Main = () => {
  return (
    <NavigationContainer>
      <Routes />
    </NavigationContainer>
  )
}
