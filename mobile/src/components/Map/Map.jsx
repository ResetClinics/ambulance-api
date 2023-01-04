import { Button } from "react-native-paper";
import { MapSection } from "../MapSection";
import { Layout } from "../../shared";
import React from "react";
import { StyleSheet } from "react-native";

export const Map = () => {
  return(
    <Layout>
      <Button
        mode="text"
        icon="chevron-left"
        style={styles.back}
      >Маршрут до места вызова
      </Button>
      <MapSection/>
    </Layout>
  )
}

const styles = StyleSheet.create({
  back: {
    marginBottom: 16,
    alignItems: "flex-start",
    marginLeft: -15,
  }
});
